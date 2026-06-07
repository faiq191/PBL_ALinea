<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message->load(['sender', 'receiver']);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->message->receiver_id),
            new PrivateChannel('chat.' . $this->message->sender_id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'sender_id' => $this->message->sender_id,
                'receiver_id' => $this->message->receiver_id,
                'message' => $this->message->message,
                'attachment_path' => $this->message->attachment_path ? (($this->message->attachment_type === 'gmaps' || str_starts_with($this->message->attachment_path, 'http')) ? $this->message->attachment_path : asset('storage/' . $this->message->attachment_path)) : null,
                'attachment_name' => $this->message->attachment_name,
                'attachment_type' => $this->message->attachment_type,
                'is_read' => $this->message->is_read,
                'created_at' => $this->message->created_at->toISOString(),
                'created_at_formatted' => $this->message->created_at->timezone('Asia/Jakarta')->format('M j, Y | g:i A'),
            ]
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'MessageSent';
    }
}
