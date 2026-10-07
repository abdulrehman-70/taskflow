<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendProjectInvitationRequest;
use App\Models\Project;
use App\Models\ProjectInvitation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use App\Notifications\ProjectInvitationNotification;
use Illuminate\Support\Facades\Notification;

class ProjectInvitationController extends Controller
{
  public function send(SendProjectInvitationRequest $request,Project $project): JsonResponse {
        Gate::authorize('inviteMember', $project);

        $email = $request->validated('email');

        if ($project->members()
            ->whereHas('user', fn ($query) => $query->where('email', $email))
            ->exists()) {
            return response()->json([
                'message' => 'This user is already a member of the project.',
            ], 422);
        }

        $invitation = ProjectInvitation::create([
            'project_id' => $project->id,
            'invited_by' => $request->user()->id,
            'email' => $email,
            'token' => Str::random(64),
            'status' => 'pending',
            'expires_at' => now()->addDays(3),
        ]);

        Notification::route('mail', $invitation->email)
            ->notify(
                new ProjectInvitationNotification(
                    $project,
                    $invitation->token
                )
            );

        return response()->json([
            'message' => 'Project invitation sent successfully.',
            'data' => [
                'invitation' => [
                    'id' => $invitation->id,
                    'email' => $invitation->email,
                    'status' => $invitation->status,
                    'expires_at' => $invitation->expires_at,
                ],
            ],
        ], 201);
    }

    public function show(string $token): JsonResponse
    {
        $invitation = ProjectInvitation::with('project')
            ->where('token', $token)
            ->first();

        if (! $invitation) {
            return response()->json([
                'message' => 'Invalid invitation.',
            ], 404);
        }

        if ($invitation->status !== 'pending') {
            return response()->json([
                'message' => 'This invitation is no longer available.',
            ], 422);
        }

        if ($invitation->expires_at->isPast()) {
            $invitation->update([
                'status' => 'expired',
            ]);

            return response()->json([
                'message' => 'This invitation has expired.',
            ], 422);
        }

        return response()->json([
            'data' => [
                'project' => [
                    'id' => $invitation->project->id,
                    'name' => $invitation->project->name,
                ],
                'email' => $invitation->email,
                'status' => $invitation->status,
                'expires_at' => $invitation->expires_at,
            ],
        ]);
    }

    public function accept(string $token): JsonResponse
    {
        $invitation = ProjectInvitation::where('token', $token)->first();

        if (! $invitation) {
            return response()->json([
                'message' => 'Invalid invitation.',
            ], 404);
        }

        if ($invitation->status !== 'pending') {
            return response()->json([
                'message' => 'This invitation is no longer available.',
            ], 422);
        }

        if ($invitation->expires_at->isPast()) {
            $invitation->update([
                'status' => 'expired',
            ]);

            return response()->json([
                'message' => 'This invitation has expired.',
            ], 422);
        }

        $user = request()->user();

        if ($user->email !== $invitation->email) {
            return response()->json([
                'message' => 'This invitation was sent to a different email address.',
            ], 403);
        }

        if ($invitation->project->members()
            ->where('user_id', $user->id)
            ->exists()) {
            return response()->json([
                'message' => 'You are already a member of this project.',
            ], 422);
        }

        $invitation->project->members()->create([
            'user_id' => $user->id,
            'role' => 'member',
            'joined_at' => now(),
        ]);

        $invitation->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return response()->json([
            'message' => 'Project invitation accepted successfully.',
        ]);
    }
}
