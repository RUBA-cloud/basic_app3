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

    public array $transpartationWay;

    public function __construct($transpartationWay)
    {
        $this->transpartationWay = is_array($transpartationWay)
            ? $transpartationWay
            : (method_exists($transpartationWay, 'toArray') ? $transpartationWay->toArray() : (array) $transpartationWay);
    }

    public function broadcastOn(): array
    {
        return [new Channel('transpartation_way_channel')];
    }

    public function broadcastAs(): string
    {
        return 'transpartation_way_updated';
    }

    public function broadcastWith(): array
    {
        return ['transpartationWay' => $this->transpartationWay];
    }
}
