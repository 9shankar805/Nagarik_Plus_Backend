<?php
namespace App\Jobs;

use App\Models\Reminder;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendReminderNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(public readonly Reminder $reminder) {}

    public function handle(NotificationService $notificationService): void
    {
        $user = $this->reminder->user;
        if (!$user || $user->isBanned()) return;
        if (!$user->hasNotificationEnabled('document_reminders')) return;

        $notificationService->sendToUser(
            $user,
            'Document Reminder: ' . $this->reminder->title,
            $this->reminder->description ?? 'Your document expires soon. Please take action.',
            [
                'type'        => 'reminder',
                'reminder_id' => (string) $this->reminder->id,
                'document_id' => (string) ($this->reminder->document_id ?? ''),
            ]
        );

        $this->reminder->update(['last_notified_at' => now()]);
    }
}
