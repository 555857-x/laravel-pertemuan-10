<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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
        $technologyIds = Technology::pluck('id');

        for ($i = 1; $i <= 10; $i++) {
            $name = 'Project Judul Ke-' . $i;

            $project = Project::create([
                'name' => $name,
                'slug' => Str::slug($name),           // P11
                'category_id' => $categoryIds->random(),
                'short_description' => 'Ringkasan project judul ke-' . $i,
                'description' => 'Deskripsi untuk project judul ke-' . $i,
                'status' => $i % 2 === 0 ? 'published' : 'draft',
                'project_date' => now()->subDays($i * 7),
                'started_at' => now()->subDays($i * 7),
                'budget' => $i * 250000,
            ]);

            // P11: isi pivot project_technology (1-3 technology acak).
            $project->technologies()->attach($technologyIds->random(rand(1, 3)));
        }
    }
}
