<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class GitHubService
{
    public function getRepository(string $owner, string $repository): array
    {
        try {
            $response = Http::acceptJson()
                ->timeout(5)
                ->retry(3, 200)
                ->get(
                    "https://api.github.com/repos/{$owner}/{$repository}"
                );

            if ($response->notFound()) {
                throw new \RuntimeException('GitHub repository not found.');
            }

            $response->throw();

            $data = $response->json();

            return [
                'name' => $data['name'],
                'full_name' => $data['full_name'],
                'description' => $data['description'],
                'url' => $data['html_url'],
                'stars' => $data['stargazers_count'],
                'forks' => $data['forks_count'],
                'open_issues' => $data['open_issues_count'],
                'default_branch' => $data['default_branch'],
                'language' => $data['language'],
                'is_private' => $data['private'],
            ];
        } catch (RequestException $exception) {
            throw new \RuntimeException(
                'Unable to retrieve repository information from GitHub.',
                0,
                $exception
            );
        }
    }
}
