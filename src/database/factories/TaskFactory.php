<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'created_by' => User::factory(),
            'assigned_to' => null,
            'title' => fake()->sentence(5),
            'description' => fake()->optional(0.8)->paragraph(),
            'status' => fake()->randomElement([
                'todo',
                'todo',
                'in_progress',
                'in_progress',
                'stuck',
                'review',
                'completed',
                'cancelled',
            ]),
            'priority' => fake()->randomElement([
                'low',
                'medium',
                'medium',
                'high',
                'urgent',
            ]),
            'due_date' => fake()->optional(0.85)->dateTimeBetween(
                '-3 months',
                '+3 months'
            )?->format('Y-m-d'),
        ];
    }
}
