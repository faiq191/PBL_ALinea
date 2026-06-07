<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessagesRead implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $readerId;
    public $senderId;

    /**
     * Create a new event instance.
     */
    public function __construct($readerId, $senderId)
    {
        $this->readerId = $readerId;
        $this->senderId = $senderId;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // Kirim event ke pengirim asli (senderId) agar UI-nya berubah jadi dibaca (centang dua biru)
        return [
            new PrivateChannel('chat.' . $this->senderId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'MessagesRead';
    }
}
