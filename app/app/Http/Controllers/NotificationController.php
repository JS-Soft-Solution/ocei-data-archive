<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);

        // Mark all unread notifications as read when viewing this page
        Auth::user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);

        $notifications = Auth::user()
            ->notifications()
            ->paginate($perPage);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Get unread notifications count (API endpoint).
     */
    public function getUnreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Notification marked as read']);
        }

        return back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
        }

        return back()->with('success', 'All notifications marked as read');
    }

    /**
     * Delete a notification.
     */
    public function destroy($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notification->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Notification deleted']);
        }

        return back()->with('success', 'Notification deleted');
    }
}
