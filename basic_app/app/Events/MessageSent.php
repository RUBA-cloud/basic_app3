<?php
// app/Events/MessageSent.php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatMessage $message;

    public function __construct(ChatMessage $message)
    {
        // Ensure relations are available for broadcast payload (optional)
        $this->message = $message->loadMissing([
            'sender:id,name,avatar_path',
            'receiver:id,name,avatar_path',
        ]);
    }

    /**
     * Broadcast on *both* participants' private channels.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.user.' . $this->message->receiver_id),
            new PrivateChannel('chat.user.' . $this->message->sender_id),
        ];
    }

    /**
     * Custom event name clients will bind to.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Keep payload compact & UI-friendly.
     */
    public function broadcastWith(): array
    {
        return [
            'id'         => $this->message->id,
            'message'    => $this->message->message,
            'created_at' => optional($this->message->created_at)->toIso8601String(),

            'sender' => [
                'id'          => $this->message->sender_id,
                'name'        => $this->message->sender?->name,
                'avatar_path' => $this->message->sender?->avatar_path,
            ],

            'receiver' => [
                'id'          => $this->message->receiver_id,
                'name'        => $this->message->receiver?->name,
                'avatar_path' => $this->message->receiver?->avatar_path,
            ],
        ];
    }

    /**
     * (Optional) Put on a specific queue.
     */
    // public string $broadcastQueue = 'broadcasts';
}
