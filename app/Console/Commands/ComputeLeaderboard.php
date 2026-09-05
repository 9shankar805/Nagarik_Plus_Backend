<?php

namespace App\Console\Commands;

use App\Models\Competition;
use App\Models\CompetitionAttempt;
use App\Models\LeaderboardEntry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ComputeLeaderboard extends Command
{
    protected $signature   = 'learning:compute-leaderboard {competition : Competition ID}';
    protected $description = 'Compute and store ranked leaderboard for a competition.';

    public function handle(): int
    {
        $id          = (int) $this->argument('competition');
        $competition = Competition::find($id);

        if (!$competition) {
            $this->error("Competition #{$id} not found.");
            return self::FAILURE;
        }

        DB::transaction(function () use ($competition) {
            // Wipe previous entries
            LeaderboardEntry::where('competition_id', $competition->id)->delete();

            // Rank: highest score first, then fastest time, then earliest submission
            $attempts = CompetitionAttempt::where('competition_id', $competition->id)
                ->orderByDesc('score_percentage')
                ->orderBy('time_taken_seconds')
                ->orderBy('created_at')
                ->get();

            foreach ($attempts as $index => $attempt) {
                $rank  = $index + 1;
                $prize = $competition->getPrizeForRank($rank);

                LeaderboardEntry::create([
                    'competition_id'     => $competition->id,
                    'user_id'            => $attempt->user_id,
                    'score_percentage'   => $attempt->score_percentage,
                    'correct_answers'    => $attempt->correct_answers,
                    'time_taken_seconds' => $attempt->time_taken_seconds,
                    'rank'               => $rank,
                    'prize_won'          => $prize,
                    'computed_at'        => now(),
                ]);

                $attempt->update(['rank' => $rank, 'prize_won' => $prize]);
            }
        });

        $total = LeaderboardEntry::where('competition_id', $competition->id)->count();
        $this->info("Leaderboard computed for competition #{$id}: {$total} entries ranked.");

        return self::SUCCESS;
    }
}
