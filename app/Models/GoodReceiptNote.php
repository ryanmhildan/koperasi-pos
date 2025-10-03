<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodReceiptNote extends Model
{
    use HasFactory;

    protected $primaryKey = 'grn_id';
    
    protected $fillable = [
        'location_id', 'receipt_date', 'reference_number'
    ];

    protected $casts = [
        'receipt_date' => 'date',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }

    public function details()
    {
        return $this->hasMany(GrnDetail::class, 'grn_id', 'grn_id');
    }
}