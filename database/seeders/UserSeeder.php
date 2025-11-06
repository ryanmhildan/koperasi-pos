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
        $admin = User::firstOrCreate(
            ['nrp' => '001'],
            [
                'username' => 'admin',
                'email' => 'admin@koperasi.com',
                'password' => Hash::make('password'),
                'full_name' => 'Administrator',
                'phone' => '081234567890',
                'join_date' => now()->toDateString(),
                'is_active' => true,
            ]
        );
        $admin->assignRole('Admin');

        // Create Kasir User
        $kasir = User::firstOrCreate(
            ['nrp' => '002'],
            [
                'username' => 'kasir1',
                'email' => 'kasir1@koperasi.com',
                'password' => Hash::make('password'),
                'full_name' => 'Kasir 1',
                'phone' => '081234567891',
                'join_date' => now()->toDateString(),
                'is_active' => true,
            ]
        );
        $kasir->assignRole('Kasir');

        // Create Anggota Users
        for ($i = 1; $i <= 5; $i++) {
            $anggota = User::firstOrCreate(
                ['nrp' => str_pad(100 + $i, 3, '0', STR_PAD_LEFT)],
                [
                    'username' => 'anggota' . $i,
                    'email' => 'anggota' . $i . '@koperasi.com',
                    'password' => Hash::make('password'),
                    'full_name' => 'Anggota ' . $i,
                    'phone' => '08123456789' . $i,
                    'join_date' => now()->subDays(rand(30, 365))->toDateString(),
                    'is_active' => true,
                ]
            );
            $anggota->assignRole('Anggota');

            // Create Wallets for each member
            $anggota->getOrCreateWallet('simpanan');
            $anggota->getOrCreateWallet('pinjaman');
            $anggota->getOrCreateWallet('operasional');
        }
    }
}
