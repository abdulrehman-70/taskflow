<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ActivityLogController extends Controller
{
    public function index(Project $project): JsonResponse
    {
        Gate::authorize('view', $project);

        $activities = ActivityLog::query()
            ->where('project_id', $project->id)
            ->with('user:id,name')
            ->latest()
            ->paginate(20);

        $activities->through(fn (ActivityLog $activity) => [
            'id' => $activity->id,
            'user' => $activity->user ? [
                'id' => $activity->user->id,
                'name' => $activity->user->name,
            ] : null,
            'task_id' => $activity->task_id,
            'action' => $activity->action,
            'description' => $activity->description,
            'metadata' => $activity->metadata,
            'created_at' => $activity->created_at,
        ]);

        return response()->json([
            'message' => 'Activity logs retrieved successfully.',
            'data' => $activities,
        ]);
    }
}
