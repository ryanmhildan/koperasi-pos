<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'location_name' => 'Gudang Utama',
                'address' => 'Jl. Raya No. 123, Jakarta',
                'is_active' => true,
            ],
            [
                'location_name' => 'Kantin Lantai 1',
                'address' => 'Gedung Utama Lantai 1',
                'is_active' => true,
            ],
            [
                'location_name' => 'Kantin Lantai 2',
                'address' => 'Gedung Utama Lantai 2',
                'is_active' => true,
            ],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}