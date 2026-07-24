<?php
namespace App\Console\Commands;

use App\Jobs\SendReminderNotificationJob;
use App\Models\Reminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DispatchDueReminders extends Command
{
    protected $signature   = 'reminders:dispatch';
    protected $description = 'Dispatch push notifications for reminders that are due today';

    public function handle(): int
    {
        // Find reminders where (due_date - days_before days) = today
        $reminders = Reminder::where('is_enabled', true)
            ->whereRaw("DATE(DATE_SUB(due_date, INTERVAL days_before DAY)) = CURDATE()")
            ->with('user')
            ->get();

        $count = 0;
        foreach ($reminders as $reminder) {
            dispatch(new SendReminderNotificationJob($reminder));
            $count++;
        }

        $this->info("Dispatched {$count} reminder notification job(s).");
        return self::SUCCESS;
    }
}
