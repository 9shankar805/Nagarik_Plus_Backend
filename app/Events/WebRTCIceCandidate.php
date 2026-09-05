<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebRTCIceCandidate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $callId;
    public $candidate;
    public $senderId;

    public function __construct($callId, $candidate, $senderId)
    {
        $this->callId = $callId;
        $this->candidate = $candidate;
        $this->senderId = $senderId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('call.' . $this->callId);
    }

    public function broadcastWith()
    {
        return [
            'call_id' => $this->callId,
            'candidate' => $this->candidate,
            'sender_id' => $this->senderId,
        ];
    }
}
