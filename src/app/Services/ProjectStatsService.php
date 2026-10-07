<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\Cache;

class ProjectStatsService
{
    public function get(Project $project): array
    {
        $cacheKey = "project:{$project->id}:stats";

        return Cache::remember(
            $cacheKey,
            now()->addMinutes(10),
            function () use ($project) {
                $tasks = $project->tasks();

                return [
                    'total_tasks' => (clone $tasks)->count(),
                    'todo' => (clone $tasks)->where('status', 'todo')->count(),
                    'in_progress' => (clone $tasks)->where('status', 'in_progress')->count(),
                    'stuck' => (clone $tasks)->where('status', 'stuck')->count(),
                    'review' => (clone $tasks)->where('status', 'review')->count(),
                    'completed' => (clone $tasks)->where('status', 'completed')->count(),
                    'cancelled' => (clone $tasks)->where('status', 'cancelled')->count(),
                    'overdue' => (clone $tasks)
                        ->whereNotNull('due_date')
                        ->whereDate('due_date', '<', today())
                        ->whereNotIn('status', ['completed', 'cancelled'])
                        ->count(),
                ];
            }
        );
    }

    public function clear(Project $project): void
    {
        Cache::forget("project:{$project->id}:stats");
    }
}
