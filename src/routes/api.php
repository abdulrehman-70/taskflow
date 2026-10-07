<?php

use App\Http\Controllers\Api\V1\ActivityLogController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\GitHubController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\ProjectInvitationController;
use App\Http\Controllers\Api\V1\ProjectMemberController;
use App\Http\Controllers\Api\V1\ProjectStatsController;
use App\Http\Controllers\Api\V1\TaskAttachmentController;
use App\Http\Controllers\Api\V1\TaskCommentController;
use App\Http\Controllers\Api\V1\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    // Authenticaion Related Routes

    Route::middleware('throttle:auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::middleware('throttle:email-verification')->group(function () {
        Route::post('/email/resend', [AuthController::class, 'resendVerificationEmail']);
    });


    Route::middleware('throttle:password-reset')->group(function () {
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });


    Route::get('/reset-password/{token}', function (string $token) {
            return response()->json([
                'token' => $token,
            ]);
        })->name('password.reset');


    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

    Route::get('/projects/invitations/{token}', [ProjectInvitationController::class, 'show']);


    Route::middleware('auth:sanctum', 'throttle:api')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);

         Route::post('/projects', [ProjectController::class, 'store']);
         Route::get('/projects', [ProjectController::class, 'index']);
         Route::get('/projects/{project}', [ProjectController::class, 'show']);
         Route::put('/projects/{project}', [ProjectController::class, 'update']);
         Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);

         Route::post('/projects/{project}/members', [ProjectMemberController::class, 'store']);
         Route::get('/projects/{project}/members', [ProjectMemberController::class, 'index']);
         Route::delete('/projects/{project}/members/{user}', [ProjectMemberController::class, 'destroy']);
         Route::post('/projects/{project}/invitations', [ProjectInvitationController::class, 'send']);


        Route::post('/projects/invitations/{token}/accept', [ProjectInvitationController::class, 'accept']);

        Route::post('/projects/{project}/tasks', [TaskController::class, 'store']);
        Route::get('/projects/{project}/tasks',[TaskController::class, 'index']);
        Route::get('/projects/{project}/tasks/{task}', [TaskController::class, 'show']);
        Route::put('/projects/{project}/tasks/{task}',[TaskController::class, 'update']);
        Route::delete('/projects/{project}/tasks/{task}', [TaskController::class, 'destroy']);

        Route::post('/projects/{project}/tasks/{task}/comments',[TaskCommentController::class, 'store']);
        Route::get('/projects/{project}/tasks/{task}/comments',[TaskCommentController::class, 'index']);
        Route::put('/projects/{project}/tasks/{task}/comments/{comment}', [TaskCommentController::class, 'update']);
        Route::delete('/projects/{project}/tasks/{task}/comments/{comment}',[TaskCommentController::class, 'destroy']);


        Route::post('/projects/{project}/tasks/{task}/attachments', [TaskAttachmentController::class, 'store']);
        Route::get('/projects/{project}/tasks/{task}/attachments', [TaskAttachmentController::class, 'index']);
        Route::get('/projects/{project}/tasks/{task}/attachments/{attachment}', [TaskAttachmentController::class, 'show']);
        Route::delete('/projects/{project}/tasks/{task}/attachments/{attachment}', [TaskAttachmentController::class, 'destroy']);

        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

        Route::get('/projects/{project}/activity', [ActivityLogController::class, 'index']);

        Route::get('/projects/{project}/stats', [ProjectStatsController::class, 'show']);

        Route::get('/github/repositories/{owner}/{repository}', [GitHubController::class, 'repository']);

    });
});


