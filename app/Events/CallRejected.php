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

class CallRejected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $call;

    public function __construct(Call $call)
    {
        $this->call = $call;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('users.' . $this->call->caller_id);
    }

    public function broadcastWith()
    {
        return [
            'call_id' => $this->call->id,
            'receiver_id' => $this->call->receiver_id,
            'receiver_name' => $this->call->receiver->name,
            'status' => $this->call->status,
            'ended_at' => $this->call->ended_at,
        ];
    }
}
