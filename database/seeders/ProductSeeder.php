<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        for ($i = 0; $i < 50; $i++) {
            Product::create([
                'product_code' => 'P' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'product_name' => $faker->word,
                'category_id' => $faker->numberBetween(1, 5),
                'unit_id' => $faker->numberBetween(1, 6),
                'selling_price' => null,
                'is_stock_item' => true,
                'product_type' => 'retail',
                'minimum_stock' => 10,
                'track_expiry' => false,
                'is_active' => true,
            ]);
        }
    }
}
