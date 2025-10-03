<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesTransaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'transaction_id';
    
    protected $fillable = [
        'transaction_number', 'user_id', 'cashier_id', 'drawer_id',
        'card_id', 'transaction_date', 'transaction_time', 'sub_total',
        'discount', 'total_amount', 'payment_method', 'status', 'notes'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'sub_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id', 'user_id');
    }

    public function cashDrawer()
    {
        return $this->belongsTo(CashDrawer::class, 'drawer_id', 'drawer_id');
    }

    public function creditCard()
    {
        return $this->belongsTo(UserCreditCard::class, 'card_id', 'card_id');
    }

    public function details()
    {
        return $this->hasMany(SalesTransactionDetail::class, 'transaction_id', 'transaction_id');
    }
}