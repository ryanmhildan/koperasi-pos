<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellingPrice extends Model
{
    use HasFactory;

    protected $primaryKey = 'selling_id';
    
    protected $fillable = [
        'product_id', 'location_id', 'average_price', 'discount', 'selling_price'
    ];

    protected $casts = [
        'average_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }
}