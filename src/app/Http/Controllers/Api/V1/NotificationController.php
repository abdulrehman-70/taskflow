<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(10);

        $notifications->through(fn ($notification) => [
            'id' => $notification->id,
            'type' => $notification->type,
            'data' => $notification->data,
            'read_at' => $notification->read_at,
            'created_at' => $notification->created_at,
        ]);

        return response()->json([
            'message' => 'Notifications retrieved successfully.',
            'data' => $notifications,
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $count = $request->user()
            ->unreadNotifications()
            ->count();

        return response()->json([
            'message' => 'Unread notification count retrieved successfully.',
            'data' => [
                'count' => $count,
            ],
        ]);
    }

    public function markAsRead(
        Request $request,
        string $notification
    ): JsonResponse {
        $userNotification = $request->user()
            ->notifications()
            ->whereKey($notification)
            ->first();

        if (! $userNotification) {
            return response()->json([
                'message' => 'Notification not found.',
            ], 404);
        }

        if (! $userNotification->read_at) {
            $userNotification->markAsRead();
        }

        return response()->json([
            'message' => 'Notification marked as read.',
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()
            ->unreadNotifications()
            ->update([
                'read_at' => now(),
            ]);

        return response()->json([
            'message' => 'All notifications marked as read.',
        ]);
    }
}
