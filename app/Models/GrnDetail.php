<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrnDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'grn_detail_id';
    
    protected $fillable = [
        'grn_id', 'product_id', 'location_id', 'quantity', 'price', 'total_price'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function goodReceiptNote()
    {
        return $this->belongsTo(GoodReceiptNote::class, 'grn_id', 'grn_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }
}