<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminNotificationController extends Controller
{
    public function index()
    {
        $tokensCount = DeviceToken::count();
        $usersCount = User::count();
        return view('admin.notifications.index', compact('tokensCount', 'usersCount'));
    }

    public function broadcast(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'target' => 'required|string|in:all,android,ios',
        ]);

        // Simulated broadcast log
        Log::info('Broadcast push notification sent:', $validated);

        return redirect()->route('admin.notifications.index')->with('success', 'Broadcast push notification queued successfully to all target devices!');
    }
}
