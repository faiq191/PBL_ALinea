<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function getUsers(Request $request)
    {
        $userId = auth()->id();
        $search = $request->query('q');

        // Fetch user IDs that the current user has chatted with
        $chattedUserIds = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->selectRaw('CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END as contact_id', [$userId])
            ->distinct()
            ->pluck('contact_id');

        if ($search) {
            $searchedUserIds = User::where('id', '!=', $userId)
                ->where('name', 'like', '%' . $search . '%')
                ->pluck('id');
            $contactIds = $chattedUserIds->merge($searchedUserIds)->unique();
        } else {
            $contactIds = $chattedUserIds;
        }

        // Fetch detailed profile information
        $usersQuery = User::whereIn('id', $contactIds)->where('id', '!=', $userId);
        
        $users = $usersQuery->get();

        // If list is empty and not searching, suggest recent users to start chatting
        if ($users->isEmpty() && !$search) {
            $users = User::where('id', '!=', $userId)
                ->latest()
                ->take(8)
                ->get();
        }

        $formattedUsers = $users->map(function ($user) use ($userId) {
            $latestMessage = Message::where(function ($q) use ($userId, $user) {
                    $q->where('sender_id', $userId)->where('receiver_id', $user->id);
                })->orWhere(function ($q) use ($userId, $user) {
                    $q->where('sender_id', $user->id)->where('receiver_id', $userId);
                })
                ->latest()
                ->first();

            $unreadCount = Message::where('sender_id', $user->id)
                ->where('receiver_id', $userId)
                ->where('is_read', false)
                ->count();

            $avatarUrl = $user->profile_photo 
                ? (str_starts_with($user->profile_photo, 'http') ? $user->profile_photo : asset('storage/' . $user->profile_photo)) 
                : asset('Gambar/default_avatar.png');

            $messageSnippet = null;
            if ($latestMessage) {
                if ($latestMessage->message) {
                    $messageSnippet = Str::limit($latestMessage->message, 25);
                } elseif ($latestMessage->attachment_type === 'image') {
                    $messageSnippet = '📷 Foto';
                } elseif ($latestMessage->attachment_type === 'tenor') {
                    $messageSnippet = '🎬 GIF';
                } elseif ($latestMessage->attachment_type === 'gmaps') {
                    $messageSnippet = '📍 Lokasi';
                } else {
                    $messageSnippet = '📎 Lampiran';
                }
            }

            return [
                'id' => $user->id,
                'name' => $user->name,
                'profile_photo' => $avatarUrl,
                'latest_message' => $messageSnippet,
                'latest_message_time' => $latestMessage ? $latestMessage->created_at->toISOString() : null,
                'latest_message_time_human' => $latestMessage ? $latestMessage->created_at->diffForHumans() : null,
                'unread_count' => $unreadCount,
            ];
        });

        // Sort users by latest message time
        $sorted = $formattedUsers->sortByDesc('latest_message_time')->values();

        return response()->json($sorted);
    }

    public function getMessages($userId)
    {
        $myId = auth()->id();
        $targetUser = User::findOrFail($userId);

        $messages = Message::where(function ($q) use ($myId, $userId) {
                $q->where('sender_id', $myId)->where('receiver_id', $userId);
            })->orWhere(function ($q) use ($myId, $userId) {
                $q->where('sender_id', $userId)->where('receiver_id', $myId);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender_id' => $msg->sender_id,
                    'receiver_id' => $msg->receiver_id,
                    'message' => $msg->message,
                    'attachment_path' => $msg->attachment_path ? (($msg->attachment_type === 'gmaps' || str_starts_with($msg->attachment_path, 'http')) ? $msg->attachment_path : asset('storage/' . $msg->attachment_path)) : null,
                    'attachment_name' => $msg->attachment_name,
                    'attachment_type' => $msg->attachment_type,
                    'created_at' => $msg->created_at->toISOString(),
                    'created_at_formatted' => $msg->created_at->timezone('Asia/Jakarta')->format('M j, Y | g:i A'),
                ];
            });

        // Mark incoming messages as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $myId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'messages' => $messages,
            'user' => [
                'id' => $targetUser->id,
                'name' => $targetUser->name,
                'profile_photo' => $targetUser->profile_photo 
                    ? (str_starts_with($targetUser->profile_photo, 'http') ? $targetUser->profile_photo : asset('storage/' . $targetUser->profile_photo)) 
                    : asset('Gambar/default_avatar.png'),
            ]
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|max:5120',
            'attachment_url' => 'nullable|string',
            'attachment_name' => 'nullable|string',
            'attachment_type' => 'nullable|string',
        ]);

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = $request->attachment_type;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('chat_attachments', 'public');
            $attachmentName = $file->getClientOriginalName();
            if (!$attachmentType) {
                $attachmentType = str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file';
            }
        } elseif ($request->attachment_url) {
            $attachmentPath = $request->attachment_url;
            $attachmentName = $request->attachment_name ?? 'Attachment';
        }

        $msg = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_type' => $attachmentType,
        ]);

        // Broadcast event for real-time
        broadcast(new MessageSent($msg))->toOthers();

        return response()->json([
            'status' => 'success',
            'message' => [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'receiver_id' => $msg->receiver_id,
                'message' => $msg->message,
                'attachment_path' => $msg->attachment_path ? (($msg->attachment_type === 'gmaps' || str_starts_with($msg->attachment_path, 'http')) ? $msg->attachment_path : asset('storage/' . $msg->attachment_path)) : null,
                'attachment_name' => $msg->attachment_name,
                'attachment_type' => $msg->attachment_type,
                'created_at' => $msg->created_at->toISOString(),
                'created_at_formatted' => $msg->created_at->timezone('Asia/Jakarta')->format('M j, Y | g:i A'),
            ]
        ]);
    }

    public function markAsRead($userId)
    {
        Message::where('sender_id', $userId)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['status' => 'success']);
    }
}
