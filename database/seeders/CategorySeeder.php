<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Minimal satu kategori wajib ada, kalau tidak dropdown kosong
     * dan exists:categories,id menolak semua input.
     */
    public function run(): void
    {
        foreach (['Web', 'Mobile', 'Desktop'] as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
