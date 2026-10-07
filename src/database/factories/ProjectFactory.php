<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-6 months', 'now');

        return [
            'created_by' => User::factory(),
            'name' => fake()->catchPhrase() . ' Project',
            'description' => fake()->optional(0.8)->paragraph(),
            'status' => fake()->randomElement([
                'active',
                'active',
                'active',
                'completed',
                'archived',
            ]),
            'start_date' => $startDate->format('Y-m-d'),
            'due_date' => fake()->optional(0.8)->dateTimeBetween(
                $startDate,
                '+6 months'
            )?->format('Y-m-d'),
        ];
    }
}
