<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Minimal satu kategori wajib ada, kalau tidak dropdown kosong
     * dan exists:categories,id menolak semua input.
     */
    public function run(): void
    {
        foreach (['Web', 'Mobile', 'Desktop'] as $name) {
            // P11: slug dipakai filter ?category=web
            Category::updateOrCreate(['name' => $name], ['slug' => Str::slug($name)]);
        }
    }
}
