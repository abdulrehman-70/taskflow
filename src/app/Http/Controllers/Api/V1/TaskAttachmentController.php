<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskAttachmentRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class TaskAttachmentController extends Controller
{
    public function store(
        StoreTaskAttachmentRequest $request,
        Project $project,
        Task $task
    ): JsonResponse {
        Gate::authorize('view', $project);

        if ($task->project_id !== $project->id) {
            return response()->json([
                'message' => 'Task does not belong to this project.',
            ], 404);
        }

        $file = $request->file('file');

        $path = $file->store(
            "tasks/{$task->id}/attachments",
            'public'
        );

        $attachment = $task->attachments()->create([
            'uploaded_by' => $request->user()->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return response()->json([
            'message' => 'Attachment uploaded successfully.',
            'data' => [
                'attachment' => [
                    'id' => $attachment->id,
                    'task_id' => $attachment->task_id,
                    'file_name' => $attachment->file_name,
                    'file_type' => $attachment->file_type,
                    'file_size' => $attachment->file_size,
                    'url' => Storage::disk('public')
                        ->url($attachment->file_path),
                    'uploaded_by' => $attachment->uploaded_by,
                    'created_at' => $attachment->created_at,
                ],
            ],
        ], 201);
    }

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

        $attachments = $task->attachments()
            ->with('uploadedBy:id,name')
            ->latest()
            ->paginate(10);

        $attachments->through(fn (TaskAttachment $attachment) => [
            'id' => $attachment->id,
            'file_name' => $attachment->file_name,
            'file_type' => $attachment->file_type,
            'file_size' => $attachment->file_size,
            'url' => Storage::disk('public')
                ->url($attachment->file_path),
            'uploaded_by' => [
                'id' => $attachment->uploadedBy->id,
                'name' => $attachment->uploadedBy->name,
            ],
            'created_at' => $attachment->created_at,
        ]);

        return response()->json([
            'message' => 'Attachments retrieved successfully.',
            'data' => $attachments,
        ]);
    }

    public function show(
        Project $project,
        Task $task,
        TaskAttachment $attachment
    ): JsonResponse {
        Gate::authorize('view', $project);

        if ($task->project_id !== $project->id) {
            return response()->json([
                'message' => 'Task does not belong to this project.',
            ], 404);
        }

        if ($attachment->task_id !== $task->id) {
            return response()->json([
                'message' => 'Attachment does not belong to this task.',
            ], 404);
        }

        return response()->json([
            'message' => 'Attachment retrieved successfully.',
            'data' => [
                'attachment' => [
                    'id' => $attachment->id,
                    'task_id' => $attachment->task_id,
                    'file_name' => $attachment->file_name,
                    'file_type' => $attachment->file_type,
                    'file_size' => $attachment->file_size,
                    'url' => Storage::disk('public')
                        ->url($attachment->file_path),
                    'uploaded_by' => $attachment->uploaded_by,
                    'created_at' => $attachment->created_at,
                ],
            ],
        ]);
    }

    public function destroy(
        Project $project,
        Task $task,
        TaskAttachment $attachment
    ): JsonResponse {
        Gate::authorize('view', $project);

        if ($task->project_id !== $project->id) {
            return response()->json([
                'message' => 'Task does not belong to this project.',
            ], 404);
        }

        if ($attachment->task_id !== $task->id) {
            return response()->json([
                'message' => 'Attachment does not belong to this task.',
            ], 404);
        }

        if ($attachment->uploaded_by !== request()->user()->id) {
            return response()->json([
                'message' => 'You can only delete your own attachments.',
            ], 403);
        }

        Storage::disk('public')->delete($attachment->file_path);

        $attachment->delete();

        return response()->json([
            'message' => 'Attachment deleted successfully.',
        ]);
    }
}
