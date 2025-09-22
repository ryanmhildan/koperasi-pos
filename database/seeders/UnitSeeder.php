<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            [
                'unit_name' => 'Pcs',
                'description' => 'Pieces - Satuan per buah',
            ],
            [
                'unit_name' => 'Box',
                'description' => 'Box - Satuan per kotak',
            ],
            [
                'unit_name' => 'Kg',
                'description' => 'Kilogram - Satuan berat',
            ],
            [
                'unit_name' => 'Liter',
                'description' => 'Liter - Satuan volume',
            ],
            [
                'unit_name' => 'Meter',
                'description' => 'Meter - Satuan panjang',
            ],
            [
                'unit_name' => 'Pack',
                'description' => 'Pack - Satuan kemasan',
            ],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}