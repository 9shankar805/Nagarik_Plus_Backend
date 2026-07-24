<?php

namespace App\Jobs;

use App\Models\News;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BroadcastNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 60;

    public function __construct(public readonly News $news) {}

    public function handle(NotificationService $notificationService): void
    {
        $notificationService->sendToTopic(
            'news_updates',
            $this->news->title,
            $this->news->content,
            [
                'type'     => 'news',
                'news_id'  => (string) $this->news->id,
                'category' => $this->news->category,
            ]
        );
    }
}
