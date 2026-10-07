<?php

namespace App\Listeners;

use App\Events\TaskAssigned;
use App\Models\ActivityLog;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleTaskAssigned
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TaskAssigned $event): void
    {
        $task = $event->task;
        $project = $event->project;
        $assignee = $event->assignee;

        ActivityLog::create([
            'user_id' => $assignee->id,
            'project_id' => $project->id,
            'task_id' => $task->id,
            'action' => 'task_assigned',
            'description' => "Task '{$task->title}' was assigned to {$assignee->name}.",
            'metadata' => [
                'assigned_to' => $assignee->id,
            ],
        ]);

        $assignee->notify(
            new TaskAssignedNotification($task, $project)
        );
    }
}
