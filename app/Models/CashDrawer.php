<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashDrawer extends Model
{
    use HasFactory;

    protected $primaryKey = 'drawer_id';
    
    protected $fillable = [
        'user_id', 'opening_balance', 'closing_balance', 'total_sales',
        'total_cash_in', 'total_cash_out', 'shift_date', 'shift_start',
        'shift_end', 'status'
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'total_cash_in' => 'decimal:2',
        'total_cash_out' => 'decimal:2',
        'shift_date' => 'date',
        'shift_start' => 'time',
        'shift_end' => 'time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function salesTransactions()
    {
        return $this->hasMany(SalesTransaction::class, 'drawer_id', 'drawer_id');
    }
}