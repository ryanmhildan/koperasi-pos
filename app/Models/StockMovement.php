<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $primaryKey = 'movement_id';
    
    protected $fillable = [
        'product_id', 'location_id', 'movement_type', 'quantity',
        'average_price', 'total_price', 'reference_type', 'reference_id',
        'movement_date', 'notes', 'created_by'
    ];

    protected $casts = [
        'average_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'movement_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}