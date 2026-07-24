<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Models\Reminder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /** Default preference structure */
    private array $defaultPreferences = [
        'document_reminders'   => true,
        'news_updates'         => true,
        'system_announcements' => true,
        'learning_updates'     => true,
    ];

    /**
     * GET /notifications/preferences
     * Returns the authenticated user's notification preferences.
     */
    public function preferences(Request $request): JsonResponse
    {
        $user  = $request->user();
        $prefs = $user->notification_preferences ?? $this->defaultPreferences;

        // Merge with defaults so new keys always appear
        $prefs = array_merge($this->defaultPreferences, $prefs);

        return response()->json([
            'success' => true,
            'data'    => $prefs,
        ]);
    }

    /**
     * PUT /notifications/preferences
     * Saves the user's notification preferences.
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $data = $request->validate([
            'document_reminders'   => 'sometimes|boolean',
            'news_updates'         => 'sometimes|boolean',
            'system_announcements' => 'sometimes|boolean',
            'learning_updates'     => 'sometimes|boolean',
        ]);

        $user  = $request->user();
        $prefs = array_merge(
            $this->defaultPreferences,
            $user->notification_preferences ?? [],
            $data
        );

        $user->update(['notification_preferences' => $prefs]);

        return response()->json([
            'success' => true,
            'message' => 'Preferences updated.',
            'data'    => $prefs,
        ]);
    }

    /**
     * GET /notifications
     * Returns recent reminders and system notifications for the user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Upcoming reminders (next 30 days, enabled)
        $reminders = Reminder::where('user_id', $user->id)
            ->where('is_enabled', true)
            ->where('remind_at', '>=', now())
            ->orderBy('remind_at')
            ->limit(20)
            ->get(['id', 'title', 'remind_at', 'type', 'is_enabled']);

        // System notifications can be extended from a dedicated table later
        $systemNotifications = [];

        return response()->json([
            'success' => true,
            'data'    => [
                'reminders'            => $reminders,
                'system_notifications' => $systemNotifications,
            ],
        ]);
    }

    /**
     * POST /notifications/device-token
     * Saves a device FCM token for push notifications.
     */
    public function registerToken(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token'    => 'required|string',
            'platform' => 'required|in:android,ios',
        ]);

        $user = $request->user();

        // Upsert: update if token already exists, else create
        DeviceToken::updateOrCreate(
            ['token' => $data['token']],
            [
                'user_id'      => $user->id,
                'platform'     => $data['platform'],
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Device token registered.',
        ]);
    }
}
