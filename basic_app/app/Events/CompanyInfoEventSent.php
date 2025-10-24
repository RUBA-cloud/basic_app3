<?php

namespace App\Events;

use App\Models\CompanyInfo;
use Illuminate\Broadcasting\PrivateChannel;  // <— use PrivateChannel
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyInfoEventSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Payload sent to clients.
     * Keep it as a plain array for safe JSON encoding.
     *
     * @var array<string, mixed>
     */
    public array $company;

    /**
     * Optionally force a connection (otherwise uses broadcasting.default).
     * Uncomment if you want to ensure Pusher is used.
     */
    // public string $connection = 'pusher';

    /**
     * If you prefer to queue, implement ShouldBroadcast and set $queue.
     * Using ShouldBroadcastNow avoids queue config issues in dev.
     */
    // public string $queue = 'broadcasts';

    public function __construct(CompanyInfo $company)
    {
        // Only include fields you actually want to expose.
        // toArray() is fine if your model hides sensitive attributes.
        $this->company = $company->toArray();
    }

    /**
     * Broadcast on a **private** channel named "company_info".
     * On the wire this becomes "private-company_info" (what Flutter subscribes to).
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('company_info')];
    }

    /**
     * The client listens for this event name.
     */
    public function broadcastAs(): string
    {
        return 'company_info_updated';
    }

    /**
     * Final JSON payload returned to clients.
     * Matches Flutter expectation: { "company": { ... } }
     */
    public function broadcastWith(): array
    {
        return ['company' => $this->company];
    }
}
