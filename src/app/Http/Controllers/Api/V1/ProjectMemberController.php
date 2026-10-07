<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddProjectMemberRequest;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use App\Notifications\ProjectMemberAddedNotification;
use App\Notifications\ProjectMemberRemovedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ProjectMemberController extends Controller
{
    public function index(Project $project): JsonResponse
    {
        Gate::authorize('view', $project);

        $members = $project->members()
            ->with('user:id,name,email')
            ->get()
            ->map(fn (ProjectMember $member) => [
                'id' => $member->id,
                'user' => [
                    'id' => $member->user->id,
                    'name' => $member->user->name,
                    'email' => $member->user->email,
                ],
                'role' => $member->role,
                'joined_at' => $member->joined_at,
            ]);

        return response()->json([
            'message' => 'Project members retrieved successfully.',
            'data' => $members,
        ]);
    }
    public function store( AddProjectMemberRequest $request,  Project $project): JsonResponse {
        Gate::authorize('addMember', $project);

        $userId = $request->validated('user_id');

        if ($project->created_by === $userId) {
            return response()->json([
                'message' => 'The project creator is already a member of the project.',
            ], 422);
        }

        if ($project->members()->where('user_id', $userId)->exists()) {
            return response()->json([
                'message' => 'User is already a member of this project.',
            ], 422);
        }

        $member = ProjectMember::create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'role' => $request->validated('role'),
            'joined_at' => now(),
        ]);

        $member->load('user');

        $member->user->notify(
            new ProjectMemberAddedNotification(
                $project,
                $member->role
            )
        );

        return response()->json([
            'message' => 'Project member added successfully.',
            'data' => [
                'member' => [
                    'id' => $member->id,
                    'project_id' => $member->project_id,
                    'user' => [
                        'id' => $member->user->id,
                        'name' => $member->user->name,
                        'email' => $member->user->email,
                    ],
                    'role' => $member->role,
                    'joined_at' => $member->joined_at,
                ],
            ],
        ], 201);
        }

        public function destroy(Project $project, User $user): JsonResponse
        {
            Gate::authorize('removeMember', $project);

            $member = $project->members()
                ->where('user_id', $user->id)
                ->first();

            if (! $member) {
                return response()->json([
                    'message' => 'User is not a member of this project.',
                ], 404);
            }

            $user->notify(
                new ProjectMemberRemovedNotification($project)
            );

            $member->delete();

            return response()->json([
                'message' => 'Project member removed successfully.',
            ]);
        }
    }



