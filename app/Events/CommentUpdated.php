<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;

    /**
     * Daftarkan data komentar yang di-update.
     */
    public function __construct(Comment $comment)
    {
        // Muat relasi user agar nama pengirim ikut terkirim secara real-time
        $this->comment = $comment->load('user');
    }

    /**
     * Tentukan Channel tempat event ini di-broadcast.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('discussion.' . $this->comment->discussion_id),
        ];
    }

    /**
     * Nama Event yang didengar oleh Javascript di frontend.
     */
    public function broadcastAs(): string
    {
        return 'CommentUpdated';
    }
}
