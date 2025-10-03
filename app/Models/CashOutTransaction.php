<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashOutTransaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'cashout_id';
    
    protected $fillable = [
        'card_id', 'amount', 'transaction_date', 'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function creditCard()
    {
        return $this->belongsTo(UserCreditCard::class, 'card_id', 'card_id');
    }
}