<?php

namespace App\Events;

use App\Models\Call;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallInitiated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $call;

    public function __construct(Call $call)
    {
        $this->call = $call;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('users.' . $this->call->receiver_id);
    }

    public function broadcastWith()
    {
        return [
            'call_id' => $this->call->id,
            'caller_id' => $this->call->caller_id,
            'caller_name' => $this->call->caller->name,
            'caller_avatar' => $this->call->caller->avatar,
            'type' => $this->call->type,
            'status' => $this->call->status,
            'started_at' => $this->call->started_at,
        ];
    }
}
