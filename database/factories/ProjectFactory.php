<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Data acak untuk satu project.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => Str::title(fake()->words(3, true)),
            'category_id' => Category::inRandomOrder()->value('id'),
            'description' => fake()->paragraph(3),
            'status' => fake()->randomElement(['draft', 'published']),
            'started_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'budget' => fake()->numberBetween(5, 500) * 100000,
        ];
    }
}
