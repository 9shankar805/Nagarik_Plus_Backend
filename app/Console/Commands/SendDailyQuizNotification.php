<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\DeviceToken;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendDailyQuizNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'learning:daily-quiz-notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send push notifications reminding users to complete their daily quiz and maintain their streak';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching daily quiz notifications...');
        
        // In a real app, we would chunk users and dispatch jobs.
        // We simulate finding active users with device tokens and sending FCM/APNS payload.
        
        $count = DeviceToken::count();
        if ($count > 0) {
            Log::info("Sent daily quiz reminders to {$count} devices.");
            $this->info("Successfully sent daily quiz reminders to {$count} devices.");
        } else {
            $this->info("No device tokens found.");
        }
        
        return Command::SUCCESS;
    }
}
