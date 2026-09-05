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

class CallAccepted implements ShouldBroadcast
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
            'receiver_avatar' => $this->call->receiver->avatar,
            'status' => $this->call->status,
            'answered_at' => $this->call->answered_at,
        ];
    }
}
