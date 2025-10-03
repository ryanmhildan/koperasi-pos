<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $primaryKey = 'unit_id';
    
    protected $fillable = [
        'unit_name', 'description'
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'unit_id', 'unit_id');
    }
}