<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\ActivityLog;
use App\Notifications\TaskOverdueNotification;
use Illuminate\Console\Command;

class ProcessOverdueTasks extends Command
{
    protected $signature = 'tasks:process-overdue';

    protected $description = 'Process overdue tasks';

    public function handle(): int
    {
        $tasks = Task::query()
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->whereNotNull('assigned_to')
            ->with('assignee')
            ->get();

        foreach ($tasks as $task) {
            $alreadyProcessed = ActivityLog::query()
                ->where('task_id', $task->id)
                ->where('action', 'task_overdue')
                ->exists();

            if ($alreadyProcessed) {
                continue;
            }

            $task->assignee->notify(
                new TaskOverdueNotification($task)
            );

            ActivityLog::create([
                'user_id' => $task->assigned_to,
                'project_id' => $task->project_id,
                'task_id' => $task->id,
                'action' => 'task_overdue',
                'description' => "Task '{$task->title}' became overdue.",
                'metadata' => [
                    'due_date' => $task->due_date?->toDateString(),
                ],
            ]);

            $this->info("Processed overdue task #{$task->id}.");
        }

        return self::SUCCESS;
    }
}
