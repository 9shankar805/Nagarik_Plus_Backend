<?php

namespace App\Jobs;

use App\Models\DeviceToken;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendFcmNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 60;

    public function __construct(
        public readonly DeviceToken $deviceToken,
        public readonly string $title,
        public readonly string $body,
        public readonly array $data = []
    ) {}

    public function handle(NotificationService $notificationService): void
    {
        $notificationService->sendToToken($this->deviceToken, $this->title, $this->body, $this->data);
    }
}
