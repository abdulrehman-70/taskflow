<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::create([
            'created_by' => $request->user()->id,
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
            'status' => $request->validated('status'),
            'start_date' => $request->validated('start_date'),
            'due_date' => $request->validated('due_date'),
        ]);

        return response()->json([
            'message' => 'Project created successfully.',
            'data' => [
                'project' => $project,
            ],
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $projects = Project::where('created_by', $request->user()->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'message' => 'Projects retrieved successfully.',
            'data' => $projects,
        ]);
    }

    public function show(Project $project): JsonResponse
    {
        Gate::authorize('view', $project);

        return response()->json([
            'message' => 'Project retrieved successfully.',
            'data' => [
                'project' => $project,
            ],
        ]);
    }
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse {
        Gate::authorize('update', $project);
        $project->update($request->validated());

        return response()->json([
            'message' => 'Project updated successfully.',
            'data' => [
                'project' => [
                    'id' => $project->id,
                    'name' => $project->name,
                    'description' => $project->description,
                    'status' => $project->status,
                    'start_date' => $project->start_date,
                    'due_date' => $project->due_date,
                ],
            ],
        ]);
    }

    public function destroy(Project $project): JsonResponse
    {
        Gate::authorize('delete', $project);

        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully.',
        ]);
    }
}
