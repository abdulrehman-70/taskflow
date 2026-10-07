<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\GitHubService;
use Illuminate\Http\JsonResponse;

class GitHubController extends Controller
{
    public function repository(
        string $owner,
        string $repository,
        GitHubService $gitHubService
    ): JsonResponse {
        $data = $gitHubService->getRepository(
            $owner,
            $repository
        );

        return response()->json([
            'message' => 'GitHub repository retrieved successfully.',
            'data' => [
                'repository' => $data,
            ],
        ]);
    }
}
