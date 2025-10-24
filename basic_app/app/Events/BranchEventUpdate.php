<?php

namespace App\Events;

use App\Models\CompanyBranch;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BranchEventUpdate implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var array */
    public array $branch;

    // If you want to force the connection regardless of .env:
    // public string $connection = 'pusher';

    public function __construct(CompanyBranch $branch)
    {
        // Prepare a clean payload
        $this->branch = $branch->toArray();
    }

    // Public channel (no auth)
    public function broadcastOn(): Channel
    {
        // MUST match the client subscription name
        return new Channel('company_branch');
    }

    // Client listens for this name
    public function broadcastAs(): string
    {
        return 'company_branch_updated';
    }

    public function broadcastWith(): array
    {
        return ['company' => $this->branch];
    }
}
