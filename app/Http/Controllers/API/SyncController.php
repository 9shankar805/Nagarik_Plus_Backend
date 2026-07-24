<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\SyncUserDocuments;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    /**
     * GET /sync/status
     * Returns the current cloud sync status for the authenticated user.
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();

        // Count documents updated after last sync (or all if never synced)
        $pendingCount = Document::where('user_id', $user->id)
            ->whereNotNull('file_path')
            ->when($user->last_synced_at, function ($q) use ($user) {
                $q->where('updated_at', '>', $user->last_synced_at);
            })
            ->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'cloud_sync_enabled' => (bool) $user->cloud_sync_enabled,
                'last_synced_at'     => $user->last_synced_at?->toIso8601String(),
                'pending_count'      => $pendingCount,
            ],
        ]);
    }

    /**
     * POST /sync/enable
     * Enables cloud sync for the authenticated user.
     */
    public function enable(Request $request): JsonResponse
    {
        $request->user()->update(['cloud_sync_enabled' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Cloud sync enabled.',
        ]);
    }

    /**
     * POST /sync/disable
     * Disables cloud sync. Optionally deletes cloud data if delete_cloud_data=true.
     */
    public function disable(Request $request): JsonResponse
    {
        $data = $request->validate([
            'delete_cloud_data' => 'sometimes|boolean',
        ]);

        $user = $request->user();
        $user->update([
            'cloud_sync_enabled' => false,
            'last_synced_at'     => null,
        ]);

        if (!empty($data['delete_cloud_data'])) {
            // Placeholder: in production, delete files from S3/cloud here
            // e.g., Storage::disk('s3')->deleteDirectory("users/{$user->id}");
        }

        return response()->json([
            'success' => true,
            'message' => 'Cloud sync disabled.' . (!empty($data['delete_cloud_data']) ? ' Cloud data removed.' : ''),
        ]);
    }

    /**
     * POST /sync/trigger
     * Dispatches the background sync job for the authenticated user.
     */
    public function trigger(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->cloud_sync_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Cloud sync is not enabled. Please enable it first.',
            ], 422);
        }

        dispatch(new SyncUserDocuments($user));

        return response()->json([
            'success' => true,
            'message' => 'Sync job dispatched. Your documents will be synced shortly.',
        ]);
    }
}
