<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\TaskAssigned;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListTasksRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\ProjectStatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function index(
        ListTasksRequest $request,
        Project $project
    ): JsonResponse {
        Gate::authorize('view', $project);

        if (! $request->user()->can('view_task')) {
            abort(403);
        }

        $data = $request->validated();

        $query = $project->tasks();

        if (! empty($data['search'])) {
            $search = $data['search'];

            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (! empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        if (! empty($data['priority'])) {
            $query->where('priority', $data['priority']);
        }

        if (array_key_exists('assigned_to', $data)) {
            $query->where('assigned_to', $data['assigned_to']);
        }

        if (! empty($data['due_date'])) {
            $query->whereDate('due_date', $data['due_date']);
        }

        $sortBy = $data['sort_by'] ?? 'created_at';
        $sortDirection = $data['sort_direction'] ?? 'desc';

        $tasks = $query
            ->orderBy($sortBy, $sortDirection)
            ->paginate($data['per_page'] ?? 10)
            ->through(fn (Task $task) => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status,
                'priority' => $task->priority,
                'assigned_to' => $task->assigned_to,
                'due_date' => $task->due_date,
            ]);

        return response()->json([
            'message' => 'Tasks retrieved successfully.',
            'data' => $tasks,
        ]);
    }

   public function store(
    StoreTaskRequest $request,
    Project $project,
    ProjectStatsService $statsService
    ): JsonResponse {
        Gate::authorize('create', [Task::class, $project]);

        $data = $request->validated();

        if (! empty($data['assigned_to'])) {
            $isMember = $project->members()
                ->where('user_id', $data['assigned_to'])
                ->exists();

            if (! $isMember) {
                return response()->json([
                    'message' => 'The assigned user is not a member of this project.',
                ], 422);
            }
        }

        $task = $project->tasks()->create([
            'created_by' => $request->user()->id,
            'assigned_to' => $data['assigned_to'] ?? null,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'],
            'priority' => $data['priority'],
            'due_date' => $data['due_date'] ?? null,
        ]);

        $statsService->clear($project);

        return response()->json([
            'message' => 'Task created successfully.',
            'data' => [
                'task' => [
                    'id' => $task->id,
                    'project_id' => $task->project_id,
                    'created_by' => $task->created_by,
                    'assigned_to' => $task->assigned_to,
                    'title' => $task->title,
                    'description' => $task->description,
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'due_date' => $task->due_date,
                ],
            ],
        ], 201);
    }

    public function show(
        Project $project,
        Task $task
    ): JsonResponse {
        if ($task->project_id !== $project->id) {
            return response()->json([
                'message' => 'Task does not belong to this project.',
            ], 404);
        }

        Gate::authorize('view', $project);

        Gate::authorize('view', $task);

        return response()->json([
            'message' => 'Task retrieved successfully.',
            'data' => [
                'task' => [
                    'id' => $task->id,
                    'project_id' => $task->project_id,
                    'created_by' => $task->created_by,
                    'assigned_to' => $task->assigned_to,
                    'title' => $task->title,
                    'description' => $task->description,
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'due_date' => $task->due_date,
                ],
            ],
        ]);
    }

    public function update(
        UpdateTaskRequest $request,
        Project $project,
        Task $task,
        ProjectStatsService $statsService
    ): JsonResponse {
        if ($task->project_id !== $project->id) {
            return response()->json([
                'message' => 'Task does not belong to this project.',
            ], 404);
        }

        Gate::authorize('update', $task);

        $data = $request->validated();

        $oldAssignedTo = $task->assigned_to;
        $oldStatus = $task->status;

        $assignmentChanged = false;
        $newAssignedTo = null;

        if (array_key_exists('assigned_to', $data)) {
            $newAssignedTo = $data['assigned_to'] !== null
                ? (int) $data['assigned_to']
                : null;

            $assignmentChanged = $oldAssignedTo !== $newAssignedTo;

            if ($assignmentChanged) {
                Gate::authorize('assign', $task);
            }

            if ($newAssignedTo !== null) {
                $isMember = $project->members()
                    ->where('user_id', $newAssignedTo)
                    ->exists();

                if (! $isMember) {
                    return response()->json([
                        'message' => 'The assigned user is not a member of this project.',
                    ], 422);
                }

                $data['assigned_to'] = $newAssignedTo;
            }
        }

        $task->update($data);

        $statsService->clear($project);

        if (
            $assignmentChanged
            && $task->assigned_to !== null
        ) {
            $assignee = User::findOrFail($task->assigned_to);

            TaskAssigned::dispatch(
                $task,
                $project,
                $assignee,
                $request->user()
            );
        }

        if (
            array_key_exists('status', $data)
            && $oldStatus !== $task->status
        ) {
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'project_id' => $project->id,
                'task_id' => $task->id,
                'action' => 'task_status_changed',
                'description' => "Task '{$task->title}' status changed from {$oldStatus} to {$task->status}.",
                'metadata' => [
                    'old_status' => $oldStatus,
                    'new_status' => $task->status,
                ],
            ]);
        }

        return response()->json([
            'message' => 'Task updated successfully.',
            'data' => [
                'task' => [
                    'id' => $task->id,
                    'project_id' => $task->project_id,
                    'created_by' => $task->created_by,
                    'assigned_to' => $task->assigned_to,
                    'title' => $task->title,
                    'description' => $task->description,
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'due_date' => $task->due_date,
                ],
            ],
        ]);
    }

    public function destroy(
        Project $project,
        Task $task,
        ProjectStatsService $statsService
    ): JsonResponse {
        if ($task->project_id !== $project->id) {
            return response()->json([
                'message' => 'Task does not belong to this project.',
            ], 404);
        }

        Gate::authorize('delete', $task);

        $task->delete();

        $statsService->clear($project);

        return response()->json([
            'message' => 'Task deleted successfully.',
        ]);
    }
}
