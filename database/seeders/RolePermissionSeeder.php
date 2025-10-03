<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
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
            Permission::create(['name' => $permission]);
        }

        // Create Roles
        $admin = Role::create(['name' => 'Admin']);
        $kasir = Role::create(['name' => 'Kasir']);
        $anggota = Role::create(['name' => 'Anggota']);

        // Assign permissions to Admin (all permissions)
        $admin->givePermissionTo(Permission::all());

        // Assign permissions to Kasir
        $kasir->givePermissionTo([
            'access pos', 'open cash drawer', 'close cash drawer',
            'create sales', 'view sales', 'view products', 'view stock'
        ]);

        // Assign permissions to Anggota
        $anggota->givePermissionTo([
            'view simpanan', 'view pinjaman', 'view angsuran'
        ]);
    }
}