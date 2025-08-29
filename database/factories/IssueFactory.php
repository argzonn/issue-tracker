<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class IssueFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::inRandomOrder()->value('id') ?? Project::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(3),
            'status' => fake()->randomElement(['open','in_progress','closed']),
            'priority' => fake()->randomElement(['low','medium','high']),
            'due_date' => fake()->optional(0.6)->date(),
        ];
    }
}
