<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $commentId;
    public $discussionId;

    /**
     * Daftarkan ID komentar dan ID diskusi yang dihapus.
     */
    public function __construct($commentId, $discussionId)
    {
        $this->commentId = $commentId;
        $this->discussionId = $discussionId;
    }

    /**
     * Tentukan Channel tempat event ini di-broadcast.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('discussion.' . $this->discussionId),
        ];
    }

    /**
     * Nama Event yang didengar oleh Javascript di frontend.
     */
    public function broadcastAs(): string
    {
        return 'CommentDeleted';
    }
}
