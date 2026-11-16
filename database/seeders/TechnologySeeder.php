<?php

namespace Database\Seeders;

use App\Models\Technology;
use Illuminate\Database\Seeder;

class TechnologySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Laravel', 'MySQL', 'Bootstrap', 'Tailwind CSS', 'JavaScript'] as $name) {
            Technology::firstOrCreate(['name' => $name]);
        }
    }
}
