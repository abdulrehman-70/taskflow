<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskCommentRequest;
use App\Http\Requests\UpdateTaskCommentRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class TaskCommentController extends Controller
{

    public function index(
        Project $project,
        Task $task
    ): JsonResponse {
        Gate::authorize('view', $project);

        if ($task->project_id !== $project->id) {
            return response()->json([
                'message' => 'Task does not belong to this project.',
            ], 404);
        }

        $comments = $task->comments()
            ->with('user:id,name')
            ->latest()
            ->paginate(10);

        $comments->through(fn ($comment) => [
            'id' => $comment->id,
            'user' => [
                'id' => $comment->user->id,
                'name' => $comment->user->name,
            ],
            'comment' => $comment->comment,
            'created_at' => $comment->created_at,
        ]);

        return response()->json([
            'message' => 'Comments retrieved successfully.',
            'data' => $comments,
        ]);
    }


    public function store(
        StoreTaskCommentRequest $request,
        Project $project,
        Task $task
    ): JsonResponse {
        Gate::authorize('view', $project);

        if ($task->project_id !== $project->id) {
            return response()->json([
                'message' => 'Task does not belong to this project.',
            ], 404);
        }

        $comment = $task->comments()->create([
            'user_id' => $request->user()->id,
            'comment' => $request->validated('comment'),
        ]);

        return response()->json([
            'message' => 'Comment added successfully.',
            'data' => [
                'comment' => [
                    'id' => $comment->id,
                    'task_id' => $comment->task_id,
                    'user_id' => $comment->user_id,
                    'comment' => $comment->comment,
                    'created_at' => $comment->created_at,
                ],
            ],
        ], 201);
    }

    public function update(
        UpdateTaskCommentRequest $request,
        Project $project,
        Task $task,
        TaskComment $comment
        ): JsonResponse
        {
        Gate::authorize('view', $project);

        if ($task->project_id !== $project->id) {
            return response()->json([
                'message' => 'Task does not belong to this project.',
            ], 404);
        }

        if ($comment->task_id !== $task->id) {
            return response()->json([
                'message' => 'Comment does not belong to this task.',
            ], 404);
        }

        if ($comment->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'You can only update your own comments.',
            ], 403);
        }

        $comment->update([
            'comment' => $request->validated('comment'),
        ]);

        return response()->json([
            'message' => 'Comment updated successfully.',
            'data' => [
                'comment' => [
                    'id' => $comment->id,
                    'task_id' => $comment->task_id,
                    'user_id' => $comment->user_id,
                    'comment' => $comment->comment,
                    'created_at' => $comment->created_at,
                    'updated_at' => $comment->updated_at,
                ],
            ],
        ]);
    }

    public function destroy(
    Project $project,
    Task $task,
    TaskComment $comment
    ): JsonResponse
    {
        Gate::authorize('view', $project);
        if ($task->project_id !== $project->id) {
            return response()->json([
                'message' => 'Task does not belong to this project.',
            ], 404);
        }

        if ($comment->task_id !== $task->id) {
            return response()->json([
                'message' => 'Comment does not belong to this task.',
            ], 404);
        }

        if ($comment->user_id !== request()->user()->id) {
            return response()->json([
                'message' => 'You can only delete your own comments.',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully.',
        ]);
    }
}
