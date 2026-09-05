<?php

namespace App\Console\Commands;

use App\Models\Competition;
use App\Models\DeviceToken;
use App\Models\LeaderboardEntry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotifyCompetitionWinners extends Command
{
    protected $signature   = 'learning:notify-winners {competition : Competition ID}';
    protected $description = 'Send push notifications to competition winners and all participants.';

    public function handle(): int
    {
        $id          = (int) $this->argument('competition');
        $competition = Competition::find($id);

        if (!$competition) {
            $this->error("Competition #{$id} not found.");
            return self::FAILURE;
        }

        $entries = LeaderboardEntry::where('competition_id', $competition->id)
            ->orderBy('rank')
            ->with('user:id,name')
            ->get();

        if ($entries->isEmpty()) {
            $this->warn("No leaderboard entries for competition #{$id}. Run compute-leaderboard first.");
            return self::FAILURE;
        }

        $fcmKey = config('services.firebase.server_key');
        $sent   = 0;

        foreach ($entries as $entry) {
            $tokens = DeviceToken::where('user_id', $entry->user_id)
                ->pluck('token')
                ->toArray();

            if (empty($tokens)) {
                continue;
            }

            $isWinner = $entry->rank <= 3;
            $prizeMsg = $entry->prize_won > 0
                ? " You won NPR {$entry->prize_won}!"
                : '';

            $title = $isWinner
                ? "🏆 Congratulations! You ranked #{$entry->rank}!"
                : "Competition Results — {$competition->title}";

            $body = $isWinner
                ? "You finished #{$entry->rank} in {$competition->title} with {$entry->score_percentage}% score.{$prizeMsg}"
                : "Results are out for {$competition->title}. Your rank: #{$entry->rank} ({$entry->score_percentage}%).";

            $this->sendFcmNotification($fcmKey, $tokens, $title, $body, [
                'type'           => 'competition_result',
                'competition_id' => (string) $competition->id,
                'rank'           => (string) $entry->rank,
                'prize_won'      => (string) $entry->prize_won,
            ]);

            $sent++;
        }

        $this->info("Notifications sent to {$sent} participants for competition #{$id}.");

        return self::SUCCESS;
    }

    private function sendFcmNotification(?string $serverKey, array $tokens, string $title, string $body, array $data = []): void
    {
        if (!$serverKey || empty($tokens)) {
            return;
        }

        try {
            Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type'  => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'registration_ids' => $tokens,
                'notification'     => ['title' => $title, 'body' => $body, 'sound' => 'default'],
                'data'             => $data,
                'priority'         => 'high',
            ]);
        } catch (\Throwable $e) {
            Log::error('FCM notification failed: ' . $e->getMessage());
        }
    }
}
