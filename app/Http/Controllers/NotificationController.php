<?php

namespace App\Http\Controllers;

use App\Models\CustomNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->customNotifications()->paginate(15);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = CustomNotification::where('user_id', auth()->id())->findOrFail($id);
        $notification->update(['is_read' => true]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        if (request()->input('redirect') === 'false') {
            return back()->with('success', 'Notifikasi ditandai telah dibaca.');
        }

        return redirect($notification->link ?? '/');
    }

    public function markAllAsRead()
    {
        auth()->user()->customNotifications()->where('is_read', false)->update(['is_read' => true]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Semua notifikasi ditandai telah dibaca.');
    }

    public function destroy($id)
    {
        $notification = CustomNotification::where('user_id', auth()->id())->findOrFail($id);
        $notification->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }
}
