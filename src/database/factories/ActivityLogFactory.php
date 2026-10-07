<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        $action = fake()->randomElement([
            'task_created',
            'task_updated',
            'task_assigned',
            'task_status_changed',
            'comment_added',
            'attachment_uploaded',
        ]);

        return [
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
            'task_id' => fake()->optional(0.8)->randomNumber(),
            'action' => $action,
            'description' => fake()->sentence(),
            'metadata' => [
                'source' => 'seeder',
            ],
        ];
    }
}
