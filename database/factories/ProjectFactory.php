<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * (Optional in newer Laravel, but explicit is nice.)
     */
    protected $model = Project::class;

    public function definition(): array
    {
        // 30% of projects have no dates
        if ($this->faker->boolean(30)) {
            $start = null;
            $deadline = null;
        } else {
            $startDt    = $this->faker->dateTimeBetween('-2 months', 'now');
            $deadlineDt = (clone $startDt)->modify('+' . random_int(10, 60) . ' days');

            $start    = $startDt->format('Y-m-d');
            $deadline = $deadlineDt->format('Y-m-d');
        }

        return [
            'name'        => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'start_date'  => $start,
            'deadline'    => $deadline,

            // Option C fields:
            'user_id'     => null,    // set in seeder when you want an owner
            'is_public'   => false,   // default private; toggle in seeder or via form
        ];
    }
}
