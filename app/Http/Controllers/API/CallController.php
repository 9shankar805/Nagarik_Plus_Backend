<?php

namespace App\Http\Controllers\API;

use App\Events\CallAccepted;
use App\Events\CallEnded;
use App\Events\CallInitiated;
use App\Events\CallRejected;
use App\Events\WebRTCAnswer;
use App\Events\WebRTCIceCandidate;
use App\Events\WebRTCOffer;
use App\Http\Controllers\Controller;
use App\Models\Call;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\TurnService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CallController extends Controller
{
    protected TurnService $turnService;
    protected NotificationService $notificationService;

    public function __construct(TurnService $turnService, NotificationService $notificationService)
    {
        $this->turnService = $turnService;
        $this->notificationService = $notificationService;
    }

    /**
     * Get TURN credentials for WebRTC
     */
    public function turnCredentials()
    {
        return response()->json($this->turnService->iceServers());
    }

    /**
     * Initiate a call
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'type' => 'required|in:audio,video',
        ]);

        $call = Call::create([
            'caller_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'type' => $request->type,
            'status' => 'initiated',
            'started_at' => now(),
        ]);

        $call->load('caller');
        broadcast(new CallInitiated($call));

        // Send push notification to receiver
        $receiver = User::find($request->receiver_id);
        $callType = $request->type === 'video' ? 'Video' : 'Audio';
        $this->notificationService->sendToUser(
            $receiver,
            "Incoming {$callType} Call",
            "{$call->caller->name} is calling you",
            [
                'type' => 'incoming_call',
                'call_id' => $call->id,
                'caller_id' => $call->caller_id,
                'caller_name' => $call->caller->name,
                'caller_avatar' => $call->caller->avatar,
                'call_type' => $request->type,
            ]
        );

        return response()->json([
            'call_id' => $call->id,
            'status' => 'initiated',
        ], 201);
    }

    /**
     * Accept a call
     */
    public function accept(Request $request)
    {
        $request->validate([
            'call_id' => 'required|exists:calls,id',
        ]);

        $call = Call::where('id', $request->call_id)
            ->where('receiver_id', Auth::id())
            ->where('status', 'initiated')
            ->firstOrFail();

        $call->update([
            'status' => 'accepted',
            'answered_at' => now(),
        ]);

        $call->load('receiver');
        broadcast(new CallAccepted($call));

        // Send push notification to caller
        $caller = User::find($call->caller_id);
        $this->notificationService->sendToUser(
            $caller,
            "Call Accepted",
            "{$call->receiver->name} accepted your call",
            [
                'type' => 'call_accepted',
                'call_id' => $call->id,
                'receiver_id' => $call->receiver_id,
                'receiver_name' => $call->receiver->name,
            ]
        );

        return response()->json([
            'call_id' => $call->id,
            'status' => 'accepted',
        ]);
    }

    /**
     * Reject a call
     */
    public function reject(Request $request)
    {
        $request->validate([
            'call_id' => 'required|exists:calls,id',
        ]);

        $call = Call::where('id', $request->call_id)
            ->where('receiver_id', Auth::id())
            ->where('status', 'initiated')
            ->firstOrFail();

        $call->update([
            'status' => 'rejected',
            'ended_at' => now(),
        ]);

        $call->load('receiver');
        broadcast(new CallRejected($call));

        // Send push notification to caller
        $caller = User::find($call->caller_id);
        $this->notificationService->sendToUser(
            $caller,
            "Call Rejected",
            "{$call->receiver->name} rejected your call",
            [
                'type' => 'call_rejected',
                'call_id' => $call->id,
                'receiver_id' => $call->receiver_id,
                'receiver_name' => $call->receiver->name,
            ]
        );

        return response()->json([
            'call_id' => $call->id,
            'status' => 'rejected',
        ]);
    }

    /**
     * End a call
     */
    public function end(Request $request)
    {
        $request->validate([
            'call_id' => 'required|exists:calls,id',
        ]);

        $call = Call::where('id', $request->call_id)
            ->where(function ($query) {
                $query->where('caller_id', Auth::id())
                    ->orWhere('receiver_id', Auth::id());
            })
            ->whereIn('status', ['initiated', 'accepted'])
            ->firstOrFail();

        $duration = 0;
        if ($call->answered_at) {
            $duration = $call->answered_at->diffInSeconds(now());
        }

        $call->update([
            'status' => 'ended',
            'ended_at' => now(),
            'duration' => $duration,
        ]);

        broadcast(new CallEnded($call));

        // Send push notification to both parties
        $caller = User::find($call->caller_id);
        $receiver = User::find($call->receiver_id);
        
        $this->notificationService->sendToUser(
            $caller,
            "Call Ended",
            "Call duration: {$this->formatDuration($duration)}",
            [
                'type' => 'call_ended',
                'call_id' => $call->id,
                'duration' => $duration,
            ]
        );

        $this->notificationService->sendToUser(
            $receiver,
            "Call Ended",
            "Call duration: {$this->formatDuration($duration)}",
            [
                'type' => 'call_ended',
                'call_id' => $call->id,
                'duration' => $duration,
            ]
        );

        return response()->json([
            'call_id' => $call->id,
            'status' => 'ended',
            'duration' => $duration,
        ]);
    }

    private function formatDuration($seconds)
    {
        if ($seconds < 60) {
            return "{$seconds}s";
        }
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        return "{$minutes}m {$remainingSeconds}s";
    }

    /**
     * Get call history for authenticated user
     */
    public function history()
    {
        $calls = Call::where(function ($query) {
            $query->where('caller_id', Auth::id())
                ->orWhere('receiver_id', Auth::id());
        })
        ->with(['caller:id,name,avatar', 'receiver:id,name,avatar'])
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        return response()->json($calls);
    }

    /**
     * Get call details
     */
    public function show($id)
    {
        $call = Call::where('id', $id)
            ->where(function ($query) {
                $query->where('caller_id', Auth::id())
                    ->orWhere('receiver_id', Auth::id());
            })
            ->with(['caller:id,name,avatar', 'receiver:id,name,avatar'])
            ->firstOrFail();

        return response()->json($call);
    }

    /**
     * Send WebRTC offer
     */
    public function sendOffer(Request $request)
    {
        $request->validate([
            'call_id' => 'required|exists:calls,id',
            'offer' => 'required',
        ]);

        $call = Call::where('id', $request->call_id)
            ->where(function ($query) {
                $query->where('caller_id', Auth::id())
                    ->orWhere('receiver_id', Auth::id());
            })
            ->firstOrFail();

        broadcast(new WebRTCOffer($call->id, $request->offer, Auth::id()));

        return response()->json(['success' => true]);
    }

    /**
     * Send WebRTC answer
     */
    public function sendAnswer(Request $request)
    {
        $request->validate([
            'call_id' => 'required|exists:calls,id',
            'answer' => 'required',
        ]);

        $call = Call::where('id', $request->call_id)
            ->where(function ($query) {
                $query->where('caller_id', Auth::id())
                    ->orWhere('receiver_id', Auth::id());
            })
            ->firstOrFail();

        broadcast(new WebRTCAnswer($call->id, $request->answer, Auth::id()));

        return response()->json(['success' => true]);
    }

    /**
     * Send WebRTC ICE candidate
     */
    public function sendIceCandidate(Request $request)
    {
        $request->validate([
            'call_id' => 'required|exists:calls,id',
            'candidate' => 'required',
        ]);

        $call = Call::where('id', $request->call_id)
            ->where(function ($query) {
                $query->where('caller_id', Auth::id())
                    ->orWhere('receiver_id', Auth::id());
            })
            ->firstOrFail();

        broadcast(new WebRTCIceCandidate($call->id, $request->candidate, Auth::id()));

        return response()->json(['success' => true]);
    }
}
