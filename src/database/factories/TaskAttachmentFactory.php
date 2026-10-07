<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskAttachment>
 */
class TaskAttachmentFactory extends Factory
{
    protected $model = TaskAttachment::class;

    public function definition(): array
    {
        $extension = fake()->randomElement([
            'pdf',
            'docx',
            'xlsx',
            'png',
            'jpg',
            'zip',
        ]);

        return [
            'task_id' => Task::factory(),
            'uploaded_by' => User::factory(),
            'file_name' => fake()->words(3, true) . '.' . $extension,
            'file_path' => 'tasks/attachments/' . fake()->uuid() . '.' . $extension,
            'file_type' => match ($extension) {
                'pdf' => 'application/pdf',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'zip' => 'application/zip',
            },
            'file_size' => fake()->numberBetween(
                10_000,
                5_000_000
            ),
        ];
    }
}
