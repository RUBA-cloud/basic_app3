<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TranspartationWayEventUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public $transpartationWay;
    public function __construct($transpartationWay)
    {
        //
        $this->transpartationWay = $transpartationWay;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('transpartation_way_channel'),
        ];
    }


     public function broadcastAs(): string
    {
        return 'transpartation_way_updated';
    }
     public function broadcastWith(): array  {
        return ['transpartationWay' => $this->transpartationWay->toArray()];
    }



}

