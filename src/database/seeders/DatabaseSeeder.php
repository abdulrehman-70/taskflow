<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting TaskFlow large dataset seeding...');

        /*
        |--------------------------------------------------------------------------
        | 1. Roles
        |--------------------------------------------------------------------------
        */

        $this->call(RolePermissionSeeder::class);

        /*
        |--------------------------------------------------------------------------
        | 2. Users
        |--------------------------------------------------------------------------
        */

        $this->command->info('Creating 1,000 users...');

        $users = User::factory(1000)->create();

        $admin = $users->first();

        $managers = $users->slice(1, 50);

        $members = $users->slice(51);

        $admin->assignRole('admin');

        foreach ($managers as $manager) {
            $manager->assignRole('manager');
        }

        foreach ($members as $member) {
            $member->assignRole('member');
        }

        $this->command->info('Users created.');

        /*
        |--------------------------------------------------------------------------
        | 3. Projects
        |--------------------------------------------------------------------------
        */

        $this->command->info('Creating 100 projects...');

        $projects = collect();

        for ($i = 0; $i < 100; $i++) {
            $creator = $users->random();

            $startDate = fake()->dateTimeBetween('-6 months', 'now');

            $project = Project::create([
                'created_by' => $creator->id,
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
            ]);

            $projects->push($project);
        }

        $this->command->info('Projects created.');

        /*
        |--------------------------------------------------------------------------
        | 4. Project Members
        |--------------------------------------------------------------------------
        */

        $this->command->info('Creating project memberships...');

        $projectMembers = collect();

        foreach ($projects as $project) {
            $creatorId = $project->created_by;

            $memberCount = fake()->numberBetween(5, 8);

            $selectedUsers = $users
                ->where('id', '!=', $creatorId)
                ->random($memberCount);

            foreach ($selectedUsers as $user) {
                $role = $user->hasRole('manager')
                    ? fake()->randomElement(['manager', 'member'])
                    : 'member';

                $member = ProjectMember::create([
                    'project_id' => $project->id,
                    'user_id' => $user->id,
                    'role' => $role,
                    'joined_at' => fake()->dateTimeBetween(
                        $project->start_date,
                        'now'
                    ),
                ]);

                $projectMembers->push($member);
            }
        }

        $this->command->info(
            "Created {$projectMembers->count()} project memberships."
        );

        /*
        |--------------------------------------------------------------------------
        | 5. Tasks
        |--------------------------------------------------------------------------
        */

        $this->command->info('Creating 5,000 tasks...');

        $tasks = collect();

        foreach ($projects as $project) {
            $projectMembersForTask = $projectMembers
                ->where('project_id', $project->id);

            $taskCount = 50;

            for ($i = 0; $i < $taskCount; $i++) {
                $assignedUser = fake()->optional(0.8)->randomElement(
                    $projectMembersForTask->pluck('user_id')->all()
                );

                $status = fake()->randomElement([
                    'todo',
                    'todo',
                    'in_progress',
                    'in_progress',
                    'stuck',
                    'review',
                    'completed',
                    'cancelled',
                ]);

                /*
                 * Some tasks should deliberately be overdue.
                 */
                $dueDate = fake()->randomElement([
                    fake()->dateTimeBetween('-3 months', '-1 day'),
                    fake()->dateTimeBetween('now', '+3 months'),
                    null,
                ]);

                $task = Task::create([
                    'project_id' => $project->id,
                    'created_by' => $project->created_by,
                    'assigned_to' => $assignedUser,
                    'title' => fake()->sentence(5),
                    'description' => fake()->optional(0.8)->paragraph(),
                    'status' => $status,
                    'priority' => fake()->randomElement([
                        'low',
                        'medium',
                        'medium',
                        'high',
                        'urgent',
                    ]),
                    'due_date' => $dueDate?->format('Y-m-d'),
                ]);

                $tasks->push($task);
            }
        }

        $this->command->info(
            "Created {$tasks->count()} tasks."
        );

        /*
        |--------------------------------------------------------------------------
        | 6. Comments
        |--------------------------------------------------------------------------
        */

        $this->command->info('Creating comments...');

        $commentCount = 0;

        foreach ($tasks as $task) {
            $projectMemberIds = $projectMembers
                ->where('project_id', $task->project_id)
                ->pluck('user_id')
                ->all();

            if (empty($projectMemberIds)) {
                continue;
            }

            $numberOfComments = fake()->numberBetween(0, 3);

            for ($i = 0; $i < $numberOfComments; $i++) {
                TaskComment::create([
                    'task_id' => $task->id,
                    'user_id' => Arr::random($projectMemberIds),
                    'comment' => fake()->paragraph(),
                ]);

                $commentCount++;
            }
        }

        $this->command->info(
            "Created {$commentCount} comments."
        );

        /*
        |--------------------------------------------------------------------------
        | 7. Attachments metadata
        |--------------------------------------------------------------------------
        */

        $this->command->info('Creating attachment metadata...');

        $attachmentCount = 0;

        foreach ($tasks->random(min(750, $tasks->count())) as $task) {
            $projectMemberIds = $projectMembers
                ->where('project_id', $task->project_id)
                ->pluck('user_id')
                ->all();

            if (empty($projectMemberIds)) {
                continue;
            }

            $extension = Arr::random([
                'pdf',
                'docx',
                'xlsx',
                'png',
                'jpg',
                'zip',
            ]);

            $mimeType = match ($extension) {
                'pdf' => 'application/pdf',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'zip' => 'application/zip',
            };

            TaskAttachment::create([
                'task_id' => $task->id,
                'uploaded_by' => Arr::random($projectMemberIds),
                'file_name' => fake()->words(3, true) . '.' . $extension,
                'file_path' => "tasks/{$task->id}/attachments/" . fake()->uuid() . ".{$extension}",
                'file_type' => $mimeType,
                'file_size' => fake()->numberBetween(
                    10_000,
                    5_000_000
                ),
            ]);

            $attachmentCount++;
        }

        $this->command->info(
            "Created {$attachmentCount} attachment records."
        );

        /*
        |--------------------------------------------------------------------------
        | 8. Activity Logs
        |--------------------------------------------------------------------------
        */

        $this->command->info('Creating activity logs...');

        $activityCount = 0;

        foreach ($tasks as $task) {
            $projectMemberIds = $projectMembers
                ->where('project_id', $task->project_id)
                ->pluck('user_id')
                ->all();

            if (empty($projectMemberIds)) {
                continue;
            }

            $numberOfActivities = fake()->numberBetween(1, 3);

            for ($i = 0; $i < $numberOfActivities; $i++) {
                $userId = Arr::random(
                    array_merge(
                        [$task->created_by],
                        $projectMemberIds
                    )
                );

                $action = Arr::random([
                    'task_created',
                    'task_updated',
                    'task_assigned',
                    'task_status_changed',
                    'comment_added',
                    'attachment_uploaded',
                ]);

                ActivityLog::create([
                    'user_id' => $userId,
                    'project_id' => $task->project_id,
                    'task_id' => $task->id,
                    'action' => $action,
                    'description' => fake()->sentence(),
                    'metadata' => [
                        'source' => 'seeder',
                    ],
                ]);

                $activityCount++;
            }
        }

        $this->command->info(
            "Created {$activityCount} activity logs."
        );

        /*
        |--------------------------------------------------------------------------
        | 9. Notifications
        |--------------------------------------------------------------------------
        */

        $this->command->info('Creating notifications...');

        $notificationCount = 0;

        foreach ($tasks->random(min(5000, $tasks->count())) as $task) {
            if (! $task->assigned_to) {
                continue;
            }

            DB::table('notifications')->insert([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => 'App\\Notifications\\TaskAssignedNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $task->assigned_to,
                'data' => json_encode([
                    'task_id' => $task->id,
                    'project_id' => $task->project_id,
                    'title' => $task->title,
                    'message' => "You have been assigned the task '{$task->title}'.",
                ]),
                'read_at' => fake()->boolean(60)
                    ? fake()->dateTimeBetween('-30 days', 'now')
                    : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $notificationCount++;
        }

        $this->command->info(
            "Created {$notificationCount} notifications."
        );

        /*
        |--------------------------------------------------------------------------
        | Finished
        |--------------------------------------------------------------------------
        */

        $this->command->info('');
        $this->command->info('======================================');
        $this->command->info('TaskFlow seeding completed.');
        $this->command->info('======================================');
        $this->command->info("Users: {$users->count()}");
        $this->command->info("Projects: {$projects->count()}");
        $this->command->info("Memberships: {$projectMembers->count()}");
        $this->command->info("Tasks: {$tasks->count()}");
        $this->command->info("Comments: {$commentCount}");
        $this->command->info("Attachments: {$attachmentCount}");
        $this->command->info("Activities: {$activityCount}");
        $this->command->info("Notifications: {$notificationCount}");
    }
}
