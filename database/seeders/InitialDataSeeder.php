<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $now = Carbon::now();

            // === USER ADMIN ===
            $admin = User::create([
                'nrp'           => '0001',
                'username'      => 'admin',
                'password_hash' => Hash::make('password'),
                'email'         => 'admin@example.com',
                'full_name'     => 'Admin Koperasi',
                'phone'         => '081234567890',
                'join_date'     => $now->toDateString(),
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);

            // === ROLES & PERMISSIONS (SPATIE) ===
            $adminRole  = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
            $kasirRole  = Role::firstOrCreate(['name' => 'Kasir', 'guard_name' => 'web']);
            $memberRole = Role::firstOrCreate(['name' => 'Member', 'guard_name' => 'web']);

            $permissions = ['manage users', 'manage products', 'view reports', 'process sales'];
            foreach ($permissions as $perm) {
                Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            }

            $adminRole->syncPermissions(Permission::all());
            $admin->assignRole('Admin');

            // === DATA CATEGORIES ===
            DB::table('categories')->insert([
                ['category_name' => 'Minuman', 'description' => 'Produk minuman', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['category_name' => 'Makanan', 'description' => 'Produk makanan', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ]);

            // === DATA UNITS ===
            DB::table('units')->insert([
                ['unit_name' => 'Pcs', 'description' => 'Per piece', 'created_at' => $now, 'updated_at' => $now],
                ['unit_name' => 'Dus', 'description' => 'Per dus', 'created_at' => $now, 'updated_at' => $now],
            ]);

            // === DATA LOCATIONS ===
            DB::table('locations')->insert([
                ['location_name' => 'Gudang Utama', 'address' => 'Jl. Raya No.1', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['location_name' => 'Toko Kantin', 'address' => 'Kantin Pabrik', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ]);

            // === DATA PRODUCTS ===
            DB::table('products')->insert([
                [
                    'product_code'   => 'P0001',
                    'barcode'        => '8991234567890',
                    'product_name'   => 'Tisu Wajah 100 Lembar',
                    'category_id'    => 1,
                    'unit_id'        => 1,
                    'selling_price'  => 11000,
                    'is_stock_item'  => true,
                    'product_type'   => 'retail',
                    'minimum_stock'  => 5,
                    'track_expiry'   => false,
                    'is_active'      => true,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]
            ]);
        });
    }
}
