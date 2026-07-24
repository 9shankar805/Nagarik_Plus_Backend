<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    private Client $http;

    public function __construct()
    {
        $this->http = new Client(['timeout' => 10]);
    }

    /**
     * Send a push notification to all registered device tokens for a user.
     * Automatically deletes tokens that FCM reports as invalid (404/410).
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        $tokens = $user->deviceTokens()->get();

        foreach ($tokens as $deviceToken) {
            $this->sendToToken($deviceToken, $title, $body, $data);
        }
    }

    /**
     * Send a push notification to a single DeviceToken.
     */
    public function sendToToken(DeviceToken $deviceToken, string $title, string $body, array $data = []): void
    {
        $fcmServerKey = config('services.fcm.server_key');

        if (!$fcmServerKey) {
            Log::warning('NotificationService: FCM server key not configured.');
            return;
        }

        $payload = [
            'to'           => $deviceToken->token,
            'notification' => [
                'title' => $title,
                'body'  => $body,
            ],
            'data' => $data,
        ];

        try {
            $this->http->post('https://fcm.googleapis.com/fcm/send', [
                'headers' => [
                    'Authorization' => 'key=' . $fcmServerKey,
                    'Content-Type'  => 'application/json',
                ],
                'json' => $payload,
            ]);

            $deviceToken->update(['last_used_at' => now()]);

        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();

            // 404 / 410 = invalid or unregistered token — delete it
            if (in_array($statusCode, [404, 410])) {
                Log::info("NotificationService: removing invalid token #{$deviceToken->id} (HTTP {$statusCode})");
                $deviceToken->delete();
            } else {
                Log::error("NotificationService: FCM error for token #{$deviceToken->id}", [
                    'status' => $statusCode,
                    'body'   => (string) $e->getResponse()->getBody(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('NotificationService: unexpected error', ['message' => $e->getMessage()]);
        }
    }

    /**
     * Send a notification to all devices subscribed to an FCM topic.
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = []): void
    {
        $fcmServerKey = config('services.fcm.server_key');

        if (!$fcmServerKey) {
            return;
        }

        $payload = [
            'to'           => '/topics/' . $topic,
            'notification' => ['title' => $title, 'body' => $body],
            'data'         => $data,
        ];

        try {
            $this->http->post('https://fcm.googleapis.com/fcm/send', [
                'headers' => [
                    'Authorization' => 'key=' . $fcmServerKey,
                    'Content-Type'  => 'application/json',
                ],
                'json' => $payload,
            ]);
        } catch (\Throwable $e) {
            Log::error('NotificationService: topic send error', ['message' => $e->getMessage()]);
        }
    }
}
