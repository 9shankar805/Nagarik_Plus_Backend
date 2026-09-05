<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\VideoClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VideoClassController extends Controller
{
    /**
     * GET /api/v1/video-classes
     * Paginated list of video classes with optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = VideoClass::active()->with('category:id,slug,name_en,name_np');

        if ($request->filled('category_id')) {
            $query->where('learning_category_id', $request->category_id);
        }

        if ($request->filled('is_live')) {
            $query->where('is_live', filter_var($request->is_live, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $videos = $query->orderByDesc('created_at')->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $videos,
        ]);
    }

    /**
     * GET /api/v1/video-classes/{id}
     * Single video class detail.
     */
    public function show(int $id): JsonResponse
    {
        $video = VideoClass::active()
            ->with('category:id,slug,name_en,name_np')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $video,
        ]);
    }

    /**
     * GET /api/v1/video-classes/live
     * Currently live or upcoming scheduled classes.
     */
    public function live(): JsonResponse
    {
        $live = VideoClass::active()
            ->where('is_live', true)
            ->whereIn('status', ['live', 'scheduled'])
            ->with('category:id,slug,name_en,name_np')
            ->orderBy('live_scheduled_at')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $live,
        ]);
    }

    /**
     * POST /api/v1/video-classes/{id}/watch  [auth]
     * Record that the authenticated user watched (or is watching) a video.
     * Body: { progress_seconds: int }  (optional — defaults to full duration)
     */
    public function markWatched(Request $request, int $id): JsonResponse
    {
        $video = VideoClass::active()->findOrFail($id);

        $data = $request->validate([
            'progress_seconds' => 'nullable|integer|min:0',
        ]);

        $progressSeconds  = $data['progress_seconds'] ?? ($video->duration_minutes * 60);
        $durationSeconds  = ($video->duration_minutes ?? 0) * 60;
        $completionPct    = $durationSeconds > 0
            ? min(100, round(($progressSeconds / $durationSeconds) * 100))
            : 100;
        $completed        = $completionPct >= 90;

        // Upsert a simple watch record in a JSON column on video_classes itself
        // (no separate watch table — track via DB directly)
        DB::table('video_watch_history')->updateOrInsert(
            ['user_id' => $request->user()->id, 'video_class_id' => $id],
            [
                'progress_seconds' => $progressSeconds,
                'completion_pct'   => $completionPct,
                'completed'        => $completed,
                'last_watched_at'  => now(),
                'updated_at'       => now(),
                'created_at'       => now(),
            ]
        );

        return response()->json([
            'success'        => true,
            'data'           => [
                'video_id'         => $id,
                'progress_seconds' => $progressSeconds,
                'completion_pct'   => $completionPct,
                'completed'        => $completed,
            ],
        ]);
    }

    /**
     * GET /api/v1/video-classes/my-history  [auth]
     * Paginated watch history for the authenticated user.
     */
    public function watchHistory(Request $request): JsonResponse
    {
        $rows = DB::table('video_watch_history as wh')
            ->join('video_classes as vc', 'vc.id', '=', 'wh.video_class_id')
            ->leftJoin('learning_categories as lc', 'lc.id', '=', 'vc.learning_category_id')
            ->where('wh.user_id', $request->user()->id)
            ->select(
                'wh.video_class_id',
                'vc.title',
                'vc.thumbnail_url',
                'vc.duration_minutes',
                'lc.name_en as category_name',
                'wh.progress_seconds',
                'wh.completion_pct',
                'wh.completed',
                'wh.last_watched_at'
            )
            ->orderByDesc('wh.last_watched_at')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $rows,
        ]);
    }
}
