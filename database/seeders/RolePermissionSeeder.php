<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // User Management
            'view users', 'create users', 'edit users', 'delete users',
            
            // Koperasi Management
            'view simpanan', 'create simpanan', 'edit simpanan', 'delete simpanan',
            'view pinjaman', 'create pinjaman', 'edit pinjaman', 'delete pinjaman',
            'view angsuran', 'create angsuran', 'edit angsuran', 'delete angsuran',
            'view cashout', 'create cashout', 'edit cashout', 'delete cashout',
            
            // Product & Inventory Management
            'view products', 'create products', 'edit products', 'delete products',
            'view stock', 'create stock', 'edit stock', 'delete stock',
            'view grn', 'create grn', 'edit grn', 'delete grn',
            
            // POS Management
            'access pos', 'open cash drawer', 'close cash drawer',
            'create sales', 'void sales', 'view sales',
            
            // Reports
            'view reports', 'export reports',
            
            // Master Data
            'view categories', 'create categories', 'edit categories', 'delete categories',
            'view units', 'create units', 'edit units', 'delete units',
            'view locations', 'create locations', 'edit locations', 'delete locations',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create Roles and assign existing permissions
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions(Permission::all());

        $kasir = Role::firstOrCreate(['name' => 'Kasir']);
        $kasir->syncPermissions([
            'access pos', 'open cash drawer', 'close cash drawer',
            'create sales', 'view sales', 'view products', 'view stock'
        ]);

        $anggota = Role::firstOrCreate(['name' => 'Anggota']);
        $anggota->syncPermissions([
            'view simpanan', 'view pinjaman', 'view angsuran'
        ]);
    }
}
