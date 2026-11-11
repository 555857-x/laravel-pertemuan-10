<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            'PHP' => 85,
            'Laravel' => 80,
            'HTML & CSS' => 90,
            'JavaScript' => 70,
        ];

        foreach ($skills as $name => $level) {
            Skill::updateOrCreate(['name' => $name], ['level' => $level]);
        }
    }
}
