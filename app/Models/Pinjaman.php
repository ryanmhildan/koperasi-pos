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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function angsuran()
    {
        return $this->hasMany(Angsuran::class, 'pinjaman_id', 'pinjaman_id');
    }
}