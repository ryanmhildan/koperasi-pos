<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $primaryKey = 'price_id';
    
    protected $fillable = [
        'product_id', 'location_id', 'average_price'
    ];

    protected $casts = [
        'average_price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }
}