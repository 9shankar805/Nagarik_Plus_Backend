<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncUserDocuments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Timeout in seconds.
     */
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(public readonly User $user)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = $this->user;

        $documents = $user->documents()
            ->whereNotNull('file_path')
            ->get(['id', 'title', 'file_path', 'updated_at']);

        Log::info("SyncUserDocuments: starting sync for user {$user->id}", [
            'document_count' => $documents->count(),
        ]);

        // TODO: Implement actual cloud upload (e.g., S3)
        // foreach ($documents as $document) {
        //     Storage::disk('s3')->put(
        //         "users/{$user->id}/documents/{$document->id}",
        //         Storage::get($document->file_path)
        //     );
        // }

        foreach ($documents as $document) {
            Log::info("SyncUserDocuments: would sync document #{$document->id} — {$document->title}");
        }

        $user->update(['last_synced_at' => now()]);

        Log::info("SyncUserDocuments: sync complete for user {$user->id}");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SyncUserDocuments: sync failed for user {$this->user->id}", [
            'error' => $exception->getMessage(),
        ]);
    }
}
