<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCreditCard extends Model
{
    use HasFactory;

    protected $primaryKey = 'card_id';
    
    protected $fillable = [
        'user_id', 'card_number', 'credit_limit', 'current_balance',
        'cash_out_limit', 'cash_out_used_this_month', 'expiry_date',
        'bank_name', 'is_active'
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'cash_out_limit' => 'decimal:2',
        'cash_out_used_this_month' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function cashOutTransactions()
    {
        return $this->hasMany(CashOutTransaction::class, 'card_id', 'card_id');
    }

    public function salesTransactions()
    {
        return $this->hasMany(SalesTransaction::class, 'card_id', 'card_id');
    }
}