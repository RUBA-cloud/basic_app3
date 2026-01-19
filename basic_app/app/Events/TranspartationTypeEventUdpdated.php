<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TranspartationTypeEventUdpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $transpartation;
    public function __construct($transpartation)
    {
        $this->transpartation = $transpartation;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('transpartation-type_channel'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'transpartation-type-updated';
    }
     public function broadcastWith(): array
    {
        return ['transpartationWay' => $this->transpartation];
    }
}
