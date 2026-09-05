<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebRTCAnswer implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $callId;
    public $answer;
    public $senderId;

    public function __construct($callId, $answer, $senderId)
    {
        $this->callId = $callId;
        $this->answer = $answer;
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
            'answer' => $this->answer,
            'sender_id' => $this->senderId,
        ];
    }
}
