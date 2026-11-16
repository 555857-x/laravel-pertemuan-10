<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ProjectTableSeeder extends Seeder
{
    /**
     * Mengisi 10 project contoh.
     */
    public function run(): void
    {
        // Tabel projects dipakai foreign key project_images, jadi cek FK
        // dimatikan sebentar supaya truncate tidak ditolak MySQL.
        Schema::disableForeignKeyConstraints();
        ProjectImage::truncate();
        Project::truncate();
        Schema::enableForeignKeyConstraints();

        $categoryIds = Category::pluck('id');

        for ($i = 1; $i <= 10; $i++) {
            Project::create([
                'name' => 'Project Judul Ke-' . $i,
                'category_id' => $categoryIds->random(),
                'description' => 'Deskripsi untuk project judul ke-' . $i,
                'status' => $i % 2 === 0 ? 'published' : 'draft',
                'started_at' => now()->subDays($i * 7),
                'budget' => $i * 250000,
            ]);
        }
    }
}
