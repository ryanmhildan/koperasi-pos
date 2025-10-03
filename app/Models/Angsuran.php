<?php
// app/Models/Angsuran.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Angsuran extends Model
{
    use HasFactory;

    protected $table = 'angsuran';
    protected $primaryKey = 'angsuran_id';
    
    protected $fillable = [
        'pinjaman_id', 
        'amount', 
        'due_date', 
        'paid_date', 
        'status', 
        'denda'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'denda' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    /**
     * Relationship to Pinjaman
     */
    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'pinjaman_id', 'pinjaman_id');
    }

    /**
     * Get user through pinjaman relationship
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Pinjaman::class,
            'pinjaman_id', // Foreign key on pinjaman table
            'user_id',     // Foreign key on users table  
            'pinjaman_id', // Local key on angsuran table
            'user_id'      // Local key on pinjaman table
        );
    }

    /**
     * Scope for pending angsuran
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for paid angsuran
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for overdue angsuran
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
                    ->orWhere(function($q) {
                        $q->where('status', 'pending')
                          ->where('due_date', '<', Carbon::now());
                    });
    }

    /**
     * Check if angsuran is overdue
     */
    public function getIsOverdueAttribute()
    {
        return $this->status === 'pending' && $this->due_date < Carbon::now();
    }

    /**
     * Calculate days overdue
     */
    public function getDaysOverdueAttribute()
    {
        if ($this->status !== 'pending' || $this->due_date >= Carbon::now()) {
            return 0;
        }
        
        return Carbon::now()->diffInDays($this->due_date);
    }

    /**
     * Get total amount including denda
     */
    public function getTotalAmountAttribute()
    {
        return $this->amount + $this->denda;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        switch ($this->status) {
            case 'paid':
                return 'green';
            case 'overdue':
                return 'red';
            case 'pending':
                return $this->is_overdue ? 'red' : 'yellow';
            default:
                return 'gray';
        }
    }

    /**
     * Get status text in Indonesian
     */
    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case 'paid':
                return 'Lunas';
            case 'overdue':
                return 'Terlambat';
            case 'pending':
                return $this->is_overdue ? 'Terlambat' : 'Belum Bayar';
            default:
                return 'Unknown';
        }
    }

    /**
     * Mark as overdue for pending angsuran past due date
     */
    public static function markOverdue()
    {
        self::where('status', 'pending')
            ->where('due_date', '<', Carbon::now())
            ->update(['status' => 'overdue']);
    }

    /**
     * Calculate denda based on days overdue
     */
    public function calculateDenda($dendaPercentage = 0.5, $maxDendaDays = 90)
    {
        if ($this->status !== 'pending' || !$this->is_overdue) {
            return 0;
        }

        $daysOverdue = min($this->days_overdue, $maxDendaDays);
        $dendaAmount = ($this->amount * $dendaPercentage / 100) * $daysOverdue;
        
        return round($dendaAmount, 2);
    }

    /**
     * Process payment for this angsuran
     */
    public function processPayment($paymentDate = null, $customDenda = null)
    {
        $paymentDate = $paymentDate ? Carbon::parse($paymentDate) : Carbon::now();
        
        // Calculate denda if not provided
        if ($customDenda === null) {
            $this->denda = $this->calculateDenda();
        } else {
            $this->denda = $customDenda;
        }

        // Mark as paid
        $this->paid_date = $paymentDate;
        $this->status = 'paid';
        $this->save();

        // Update pinjaman totals
        $pinjaman = $this->pinjaman;
        $pinjaman->total_paid += $this->total_amount;
        $pinjaman->remaining_balance -= $this->amount;
        
        // Check if all angsuran are paid
        $unpaidAngsuran = $pinjaman->angsuran()->where('status', '!=', 'paid')->count();
        if ($unpaidAngsuran === 0 || $pinjaman->remaining_balance <= 0) {
            $pinjaman->status = 'closed';
            $pinjaman->remaining_balance = 0;
        }
        
        $pinjaman->save();

        return $this;
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-update overdue status when accessing model
        static::retrieved(function ($angsuran) {
            if ($angsuran->status === 'pending' && $angsuran->due_date < Carbon::now()) {
                $angsuran->status = 'overdue';
                $angsuran->save();
            }
        });
    }
}