<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'product_code',
        'barcode',
        'product_name',
        'category_id',
        'unit_id',
        'selling_price',
        'is_stock_item',
        'product_type',
        'minimum_stock',
        'track_expiry',
        'is_active'
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'is_stock_item' => 'boolean',
        'track_expiry'  => 'boolean',
        'is_active'     => 'boolean',
    ];

    // Relasi ke Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    // Relasi ke Unit
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'unit_id');
    }

    // Relasi ke Stock
    public function stocks()
    {
        return $this->hasMany(Stock::class, 'product_id', 'product_id');
    }

    // Relasi ke Stock Movements
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'product_id', 'product_id');
    }

    // Relasi ke Price
    public function prices()
    {
        return $this->hasMany(Price::class, 'product_id', 'product_id');
    }

    // Relasi ke Selling Price
    public function sellingPrices()
    {
        return $this->hasMany(SellingPrice::class, 'product_id', 'product_id');
    }

    // Relasi ke Sales Transaction Details
    public function salesTransactionDetails()
    {
        return $this->hasMany(SalesTransactionDetail::class, 'product_id', 'product_id');
    }

    // Relasi ke GRN Details
    public function grnDetails()
    {
        return $this->hasMany(GrnDetail::class, 'product_id', 'product_id');
    }

    // Accessor for average cost price
    public function getAverageCostPriceAttribute()
    {
        // We get the average of the 'average_price' column from the related prices
        $avg = $this->prices()->avg('average_price');
        return $avg ?? 0;
    }

    // Accessor for average selling price
    public function getAverageSellingPriceAttribute()
    {
        $avg = $this->sellingPrices()->avg('selling_price');
        return $avg ?? 0;
    }
}
