<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'category_name' => 'Makanan',
                'description' => 'Kategori produk makanan',
                'is_active' => true,
            ],
            [
                'category_name' => 'Minuman',
                'description' => 'Kategori produk minuman',
                'is_active' => true,
            ],
            [
                'category_name' => 'Snack',
                'description' => 'Kategori produk camilan',
                'is_active' => true,
            ],
            [
                'category_name' => 'Alat Tulis',
                'description' => 'Kategori alat tulis kantor',
                'is_active' => true,
            ],
            [
                'category_name' => 'Elektronik',
                'description' => 'Kategori produk elektronik',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}