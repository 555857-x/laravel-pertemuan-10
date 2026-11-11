<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class RandomProjectSeeder extends Seeder
{
    /**
     * Mengisi 25 project dengan judul acak.
     */
    public function run(): void
    {
        Project::factory()->count(25)->create();
    }
}
