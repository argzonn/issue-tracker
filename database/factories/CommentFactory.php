<?php

namespace Database\Factories;

use App\Models\Issue;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'issue_id' => Issue::inRandomOrder()->value('id') ?? Issue::factory(),
            'author_name' => fake()->name(),
            'body' => fake()->sentence(12),
        ];
    }
}
