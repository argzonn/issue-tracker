<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-2 months','now');
        $deadline = (clone $start)->modify('+'.rand(10,60).' days');
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'start_date' => $start->format('Y-m-d'),
            'deadline' => $deadline->format('Y-m-d'),
        ];
    }
}
