<?php

namespace App\Console\Commands;

use App\Models\Competition;
use Illuminate\Console\Command;

class CloseCompetitions extends Command
{
    protected $signature   = 'learning:close-competitions';
    protected $description = 'Set competition status to "completed" when ends_at is reached, then auto-compute leaderboard.';

    public function handle(): int
    {
        $now = now();

        $competitions = Competition::whereIn('status', [Competition::STATUS_ONGOING, Competition::STATUS_OPEN])
            ->where('ends_at', '<=', $now)
            ->get();

        foreach ($competitions as $competition) {
            $competition->update(['status' => Competition::STATUS_COMPLETED]);

            // Auto-compute leaderboard
            $this->call('learning:compute-leaderboard', ['competition' => $competition->id]);

            $this->info("Competition #{$competition->id} '{$competition->title}' completed and leaderboard computed.");
        }

        if ($competitions->isEmpty()) {
            $this->info('No competitions to close.');
        }

        return self::SUCCESS;
    }
}
