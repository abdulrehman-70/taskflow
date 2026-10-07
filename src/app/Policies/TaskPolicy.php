<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        return $task->project->created_by === $user->id
            || $task->project->members()
                ->where('user_id', $user->id)
                ->exists();
    }

    public function create(User $user, Project $project): bool
    {
        return $user->can('create_task')
            && (
                $project->created_by === $user->id
                || $project->members()
                    ->where('user_id', $user->id)
                    ->exists()
            );
    }

    public function update(User $user, Task $task): bool
    {
        return $task->project->created_by === $user->id
            || $task->project->members()
                ->where('user_id', $user->id)
                ->exists();
    }

    public function delete(User $user, Task $task): bool
    {
        return $task->project->created_by === $user->id;
    }

    public function assign(User $user, Task $task): bool
    {
        return $task->project->created_by === $user->id
            || $task->project->members()
                ->where('user_id', $user->id)
                ->where('role', 'manager')
                ->exists();
    }
}
