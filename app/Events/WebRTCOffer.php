<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebRTCOffer implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $callId;
    public $offer;
    public $senderId;

    public function __construct($callId, $offer, $senderId)
    {
        $this->callId = $callId;
        $this->offer = $offer;
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
            'offer' => $this->offer,
            'sender_id' => $this->senderId,
        ];
    }
}
