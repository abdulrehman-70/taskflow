<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\ProjectStatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ProjectStatsController extends Controller
{
    public function show(
        Project $project,
        ProjectStatsService $statsService
    ): JsonResponse {
        Gate::authorize('view', $project);

        return response()->json([
            'message' => 'Project statistics retrieved successfully.',
            'data' => [
                'stats' => $statsService->get($project),
            ],
        ]);
    }
}
