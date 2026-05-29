<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;

    /**
     * Daftarkan data komentar baru.
     */
    public function __construct(Comment $comment)
    {
        // Muat relasi user agar foto profil & nama pengirim ikut terkirim secara real-time
        $this->comment = $comment->load('user');
    }

    /**
     * Tentukan Channel tempat event ini di-broadcast.
     * Kita gunakan Public Channel agar setup-nya super simpel & anti-gagal!
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
        return 'CommentSent';
    }
}
