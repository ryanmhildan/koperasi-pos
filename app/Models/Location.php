<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $primaryKey = 'location_id';
    
    protected $fillable = [
        'location_name', 'address', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'location_id', 'location_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'location_id', 'location_id');
    }

    public function prices()
    {
        return $this->hasMany(Price::class, 'location_id', 'location_id');
    }

    public function sellingPrices()
    {
        return $this->hasMany(SellingPrice::class, 'location_id', 'location_id');
    }
}