<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\WebRTCSignal;

class SignalingController extends Controller
{
    /**
     * Broadcast a WebRTC signaling message to the other peer.
     */
    public function signal(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:6',
            'type' => 'required|string',
            'data' => 'nullable',
            'sender' => 'required|string'
        ]);

        broadcast(new WebRTCSignal(
            $request->pin,
            $request->type,
            $request->data,
            $request->sender
        ));

        return response()->json(['success' => true]);
    }
}
