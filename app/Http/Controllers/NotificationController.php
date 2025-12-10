<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display user notifications.
     */
    public function index(Request $request): View
    {
        $notifications = auth()->user()
            ->notifications()
            ->with('user')
            ->when($request->type, function ($query, $type) {
                return $query->byType($type);
            })
            ->when($request->filter, function ($query, $filter) {
                if ($filter === 'read') {
                    return $query->read();
                } elseif ($filter === 'unread') {
                    return $query->unread();
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = auth()->user()->unread_notification_count;

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => auth()->user()->unread_notification_count,
        ]);
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsUnread();

        return response()->json([
            'success' => true,
            'unread_count' => auth()->user()->unread_notification_count,
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        auth()->user()
            ->unreadNotifications()
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }

    /**
     * Delete notification.
     */
    public function destroy(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'unread_count' => auth()->user()->unread_notification_count,
        ]);
    }

    /**
     * Get recent notifications for dropdown.
     */
    public function recent(): JsonResponse
    {
        $notifications = auth()->user()
            ->notifications()
            ->unread()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => auth()->user()->unread_notification_count,
        ]);
    }

    /**
     * Create a new notification.
     */
    public static function create($userId, $type, $title, $message, $url = null, $data = null): Notification
    {
        return Notification::createNotification($userId, $type, $title, $message, $url, $data);
    }

    /**
     * Send notification to all users.
     */
    public static function broadcast($type, $title, $message, $url = null, $data = null): void
    {
        $users = \App\Models\User::all();
        
        foreach ($users as $user) {
            Notification::createNotification($user->id, $type, $title, $message, $url, $data);
        }
    }

    /**
     * Send notification to users with specific role.
     */
    public static function broadcastToRole($role, $type, $title, $message, $url = null, $data = null): void
    {
        $users = \App\Models\User::where('role', $role)->get();
        
        foreach ($users as $user) {
            Notification::createNotification($user->id, $type, $title, $message, $url, $data);
        }
    }
}
