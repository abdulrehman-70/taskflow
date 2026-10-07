<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Project;
use App\Models\Task;
use App\Policies\ProjectPolicy;
use Illuminate\Support\Facades\Gate;
use App\Policies\TaskPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(Task::class, TaskPolicy::class);

        RateLimiter::for('auth', function (Request $request) {
        return Limit::perMinute(5)
                ->by($request->ip());
            });

            RateLimiter::for('password-reset', function (Request $request) {
                return Limit::perMinute(3)
                    ->by($request->ip());
            });

            RateLimiter::for('email-verification', function (Request $request) {
                return Limit::perMinute(3)
                    ->by($request->ip());
            });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?? $request->ip());
        });
    }
}
