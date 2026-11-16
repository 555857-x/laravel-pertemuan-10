<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Project;
use App\Models\Technology;
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
        $name = Str::title(fake()->unique()->words(3, true));

        return [
            'name' => $name,
            'slug' => Str::slug($name),                       // P11
            'short_description' => fake()->sentence(8),        // P11
            'project_date' => fake()->dateTimeBetween('-1 year', 'now'), // P11
            'category_id' => Category::inRandomOrder()->value('id'),
            'description' => fake()->paragraph(3),
            'status' => fake()->randomElement(['draft', 'published']),
            'started_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'budget' => fake()->numberBetween(5, 500) * 100000,
        ];
    }

    // P11: setiap project dari factory diberi 1-3 technology acak.
    public function configure(): static
    {
        return $this->afterCreating(function (Project $project) {
            $project->technologies()->attach(
                Technology::inRandomOrder()->limit(rand(1, 3))->pluck('id')
            );
        });
    }
}
