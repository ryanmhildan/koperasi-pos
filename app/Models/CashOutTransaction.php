<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashOutTransaction extends Model
{
    use HasFactory;

    protected $table = 'cash_out_transactions';
    protected $primaryKey = 'cash_out_id';
    
    protected $fillable = [
        'user_id', 'amount', 'transaction_date', 'notes', 'status', 'processed_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by', 'user_id');
    }
}