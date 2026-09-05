<?php

namespace App\Console\Commands;

use App\Models\Competition;
use Illuminate\Console\Command;

class OpenCompetitions extends Command
{
    protected $signature   = 'learning:open-competitions';
    protected $description = 'Set competition status to "open" when registration_open_at is reached, or to "ongoing" when starts_at is reached.';

    public function handle(): int
    {
        $now = now();

        // Draft → Open: when registration_open_at <= now (or no registration open time set)
        $openedCount = Competition::where('status', Competition::STATUS_DRAFT)
            ->where(function ($q) use ($now) {
                $q->whereNull('registration_open_at')
                  ->orWhere('registration_open_at', '<=', $now);
            })
            ->where('starts_at', '>', $now) // hasn't started yet
            ->update(['status' => Competition::STATUS_OPEN]);

        // Open → Ongoing: when starts_at <= now
        $ongoingCount = Competition::where('status', Competition::STATUS_OPEN)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>', $now)
            ->update(['status' => Competition::STATUS_ONGOING]);

        $this->info("Opened: {$openedCount} | Started (ongoing): {$ongoingCount}");

        return self::SUCCESS;
    }
}
