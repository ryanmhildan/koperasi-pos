<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesTransactionDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'detail_id';
    
    protected $fillable = [
        'transaction_id', 'product_id', 'quantity', 'selling_price', 'total_price'
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(SalesTransaction::class, 'transaction_id', 'transaction_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}