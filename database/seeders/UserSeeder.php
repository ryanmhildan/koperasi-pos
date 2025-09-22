<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserCreditCard;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'nrp' => '001',
            'username' => 'admin',
            'email' => 'admin@koperasi.com',
            'password' => Hash::make('password'),
            'full_name' => 'Administrator',
            'phone' => '081234567890',
            'join_date' => now()->toDateString(),
            'is_active' => true,
        ]);
        $admin->assignRole('Admin');

        // Create Kasir User
        $kasir = User::create([
            'nrp' => '002',
            'username' => 'kasir1',
            'email' => 'kasir1@koperasi.com',
            'password' => Hash::make('password'),
            'full_name' => 'Kasir 1',
            'phone' => '081234567891',
            'join_date' => now()->toDateString(),
            'is_active' => true,
        ]);
        $kasir->assignRole('Kasir');

        // Create Anggota Users
        for ($i = 1; $i <= 5; $i++) {
            $anggota = User::create([
                'nrp' => str_pad(100 + $i, 3, '0', STR_PAD_LEFT),
                'username' => 'anggota' . $i,
                'email' => 'anggota' . $i . '@koperasi.com',
                'password' => Hash::make('password'),
                'full_name' => 'Anggota ' . $i,
                'phone' => '08123456789' . $i,
                'join_date' => now()->subDays(rand(30, 365))->toDateString(),
                'is_active' => true,
            ]);
            $anggota->assignRole('Anggota');

            // Create Credit Card for each member
            UserCreditCard::create([
                'user_id' => $anggota->user_id,
                'card_number' => '4000' . str_pad($i, 12, '0', STR_PAD_LEFT),
                'credit_limit' => 5000000,
                'current_balance' => rand(0, 1000000),
                'cash_out_limit' => 1000000,
                'cash_out_used_this_month' => 0,
                'expiry_date' => now()->addYears(3)->format('m/Y'),
                'bank_name' => 'Bank Koperasi',
                'is_active' => true,
            ]);
        }
    }
}
