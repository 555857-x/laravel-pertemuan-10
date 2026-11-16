<?php

namespace Database\Seeders;

use App\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TechnologySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Laravel', 'MySQL', 'Bootstrap', 'Tailwind CSS', 'JavaScript'] as $name) {
            // P11: slug dipakai filter ?technology=laravel
            Technology::updateOrCreate(['name' => $name], ['slug' => Str::slug($name)]);
        }
    }
}
