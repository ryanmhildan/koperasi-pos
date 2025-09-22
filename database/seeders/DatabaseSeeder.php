<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            LocationSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
            UserSeeder::class,
        ]);
    }
}