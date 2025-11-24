<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    use HasFactory;

    protected $table = 'pinjaman';
    protected $primaryKey = 'pinjaman_id';
    
    protected $fillable = [
        'user_id', 'loan_amount', 'interest_rate', 'tenor_months',
        'loan_type', 'loan_purpose', 'loan_date', 'status',
        'is_blocked', 'total_paid', 'remaining_balance'
    ];

    protected $casts = [
        'loan_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'loan_date' => 'date',
        'is_blocked' => 'boolean',
    ];

    public function recalculateRemainingBalance()
    {
        // Hitung total sudah dibayar
        $totalPaid = $this->angsuran()->where('status', 'paid')->sum('amount');

        // Total pinjaman dari semua angsuran
        $totalLoanAmount = $this->angsuran()->sum('amount');

        // Sisa pinjaman
        $remaining = $totalLoanAmount - $totalPaid;

        $updateData = [
            'remaining_balance' => max(round($remaining, 2), 0),
            'total_paid' => $totalPaid
        ];

        // Cek jika pinjaman sudah lunas, beri toleransi jika ada selisih pembulatan
        if (round($remaining, 2) <= 0.01) {
            $updateData['status'] = 'closed';
        }

        // Update field sisa pinjaman
        $this->update($updateData);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            'closed' => 'Lunas',
            'active' => 'Aktif',
            'pending' => 'Pending',
            default => ucfirst($this->status),
        };
    }

    public function angsuran()
    {
        return $this->hasMany(Angsuran::class, 'pinjaman_id', 'pinjaman_id');
    }
}