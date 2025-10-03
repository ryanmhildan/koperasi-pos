<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Simpanan extends Model
{
    use HasFactory;

    protected $table = 'simpanan';
    protected $primaryKey = 'simpanan_id';
    
    protected $fillable = [
        'user_id', 'amount', 'transaction_date', 'description'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'denda' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'pinjaman_id', 'pinjaman_id');
    }
}