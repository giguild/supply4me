<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function index(Request $request): Response
    {
        $userId = $request->user()->id;

        $query = Notification::whereHas('recipients', fn ($q) => $q->where('user_id', $userId))
            ->with(['recipients' => fn ($q) => $q->where('user_id', $userId)]);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->whereHas('recipients', fn ($q) => $q->where('user_id', $userId)->whereNull('read_at'));
            } elseif ($request->status === 'read') {
                $query->whereHas('recipients', fn ($q) => $q->where('user_id', $userId)->whereNotNull('read_at'));
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $notifications = $query->latest()->paginate(15)->withQueryString();

        $unreadCount = NotificationRecipient::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();

        $types = Notification::select('type')->distinct()->pluck('type');

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'types' => $types,
            'filters' => $request->only(['type', 'status', 'search']),
        ]);
    }

    public function unreadCount(Request $request)
    {
        $userId = $request->user()->id;

        $count = NotificationRecipient::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();

        $notifications = Notification::whereHas('recipients', fn ($q) => $q->where('user_id', $userId))
            ->with(['recipients' => fn ($q) => $q->where('user_id', $userId)])
            ->latest()
            ->limit(8)
            ->get();

        return response()->json(['count' => $count, 'notifications' => $notifications]);
    }

    public function markRead(Request $request, Notification $notification)
    {
        $notification->markAsReadBy($request->user()->id);

        return back();
    }

    public function markAllRead(Request $request)
    {
        Notification::markAllAsReadBy($request->user()->id);

        return back();
    }

    public function destroy(Request $request, Notification $notification)
    {
        $notification->recipients()->where('user_id', $request->user()->id)->delete();

        if (!$notification->recipients()->exists()) {
            $notification->delete();
        }

        return back();
    }
}
