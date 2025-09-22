<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $primaryKey = 'user_id';
    
    protected $fillable = [
        'nrp', 'username', 'email', 'password', 'full_name', 
        'phone', 'join_date', 'is_active'
    ];

    protected $hidden = ['password', 'remember_token'];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'join_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function creditCards()
    {
        return $this->hasMany(UserCreditCard::class, 'user_id', 'user_id');
    }

    public function simpanan()
    {
        return $this->hasMany(Simpanan::class, 'user_id', 'user_id');
    }

    public function pinjaman()
    {
        return $this->hasMany(Pinjaman::class, 'user_id', 'user_id');
    }

    public function cashDrawers()
    {
        return $this->hasMany(CashDrawer::class, 'user_id', 'user_id');
    }

    public function salesAsCashier()
    {
        return $this->hasMany(SalesTransaction::class, 'cashier_id', 'user_id');
    }

    public function salesAsCustomer()
    {
        return $this->hasMany(SalesTransaction::class, 'user_id', 'user_id');
    }
}