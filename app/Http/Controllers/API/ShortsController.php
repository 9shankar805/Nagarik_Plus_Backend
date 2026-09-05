<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserShort;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ShortsController extends Controller
{
    /**
     * Upload video file
     */
    public function upload(Request $request)
    {
        $request->validate([
            'video_file' => 'required|file|mimes:mp4,mov,avi|max:102400', // Max 100MB
            'cover_image' => 'nullable|file|mimes:jpg,jpeg,png|max:10240', // Max 10MB
        ]);

        $videoFile = $request->file('video_file');
        $coverImage = $request->file('cover_image');

        // Store video file
        $videoPath = $videoFile->store('shorts/raw', 'public');
        $videoUrl = Storage::url($videoPath);

        // Store cover image if provided
        $coverImageUrl = null;
        if ($coverImage) {
            $coverPath = $coverImage->store('shorts/covers', 'public');
            $coverImageUrl = Storage::url($coverPath);
        }

        // Generate a temporary video ID
        $videoId = 'vid_' . time() . '_' . rand(1000, 9999);

        return response()->json([
            'success' => true,
            'data' => [
                'video_id' => $videoId,
                'video_url' => $videoUrl,
                'cover_image_url' => $coverImageUrl,
            ],
        ]);
    }

    /**
     * Publish short with metadata
     */
    public function publish(Request $request)
    {
        $request->validate([
            'video_id' => 'required|string',
            'video_url' => 'required|string',
            'audio_id' => 'nullable|exists:audio,id',
            'effect_id' => 'nullable|exists:effects,id',
            'caption' => 'nullable|string|max:500',
            'privacy' => 'required|in:PUBLIC,PRIVATE,F',
            'location' => 'nullable|string|max:255',
            'cover_image_url' => 'nullable|string',
        ]);

        $short = UserShort::create([
            'user_id' => Auth::id(),
            'audio_id' => $request->audio_id,
            'effect_id' => $request->effect_id,
            'video_url' => $request->video_url,
            'cover_image_url' => $request->cover_image_url,
            'caption' => $request->caption,
            'privacy' => $request->privacy,
            'location' => $request->location,
            'published_at' => now(),
        ]);

        // Increment audio usage count if audio was used
        if ($request->audio_id) {
            $audio = \App\Models\Audio::find($request->audio_id);
            if ($audio) {
                $audio->incrementUsage();
            }
        }

        // Increment effect usage count if effect was used
        if ($request->effect_id) {
            $effect = \App\Models\Effect::find($request->effect_id);
            if ($effect) {
                $effect->incrementUsage();
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'short_id' => $short->id,
                'video_url' => $short->video_url,
                'published_at' => $short->published_at,
            ],
        ]);
    }

    /**
     * Get shorts feed
     */
    public function feed(Request $request)
    {
        $request->validate([
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $page = $request->page ?? 1;
        $limit = $request->limit ?? 10;

        $shorts = UserShort::where('privacy', 'PUBLIC')
            ->whereNotNull('published_at')
            ->where('is_processed', true)
            ->with(['user:id,name,avatar', 'audio:id,title', 'effect:id,title'])
            ->orderBy('published_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $shorts->map(function ($short) {
                return [
                    'id' => $short->id,
                    'author' => [
                        'id' => $short->user->id,
                        'name' => $short->user->name,
                        'avatar_url' => $short->user->avatar,
                        'is_verified' => $short->user->is_verified ?? false,
                    ],
                    'video_url' => $short->video_url,
                    'cover_image_url' => $short->cover_image_url,
                    'caption' => $short->caption,
                    'location' => $short->location,
                    'likes_count' => $short->likes_count,
                    'comments_count' => $short->comments_count,
                    'shares_count' => $short->shares_count,
                    'views_count' => $short->views_count,
                    'audio' => $short->audio ? [
                        'id' => $short->audio->id,
                        'title' => $short->audio->title,
                    ] : null,
                    'effect' => $short->effect ? [
                        'id' => $short->effect->id,
                        'title' => $short->effect->title,
                    ] : null,
                    'published_at' => $short->published_at,
                ];
            }),
            'pagination' => [
                'current_page' => $shorts->currentPage(),
                'total_pages' => $shorts->lastPage(),
                'per_page' => $shorts->perPage(),
                'total' => $shorts->total(),
            ],
        ]);
    }

    /**
     * Get user's own shorts
     */
    public function myShorts(Request $request)
    {
        $request->validate([
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $page = $request->page ?? 1;
        $limit = $request->limit ?? 10;

        $shorts = UserShort::where('user_id', Auth::id())
            ->with(['audio:id,title', 'effect:id,title'])
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $shorts->map(function ($short) {
                return [
                    'id' => $short->id,
                    'video_url' => $short->video_url,
                    'cover_image_url' => $short->cover_image_url,
                    'caption' => $short->caption,
                    'privacy' => $short->privacy,
                    'likes_count' => $short->likes_count,
                    'comments_count' => $short->comments_count,
                    'shares_count' => $short->shares_count,
                    'views_count' => $short->views_count,
                    'is_processed' => $short->is_processed,
                    'published_at' => $short->published_at,
                    'created_at' => $short->created_at,
                ];
            }),
            'pagination' => [
                'current_page' => $shorts->currentPage(),
                'total_pages' => $shorts->lastPage(),
                'per_page' => $shorts->perPage(),
                'total' => $shorts->total(),
            ],
        ]);
    }

    /**
     * Increment view count
     */
    public function view(Request $request)
    {
        $request->validate([
            'short_id' => 'required|exists:user_shorts,id',
        ]);

        $short = UserShort::find($request->short_id);
        $short->incrementViews();

        return response()->json([
            'success' => true,
            'data' => [
                'views_count' => $short->views_count,
            ],
        ]);
    }

    /**
     * Like/Unlike short
     */
    public function toggleLike(Request $request)
    {
        $request->validate([
            'short_id' => 'required|exists:user_shorts,id',
        ]);

        $short = UserShort::find($request->short_id);
        
        // Check if user already liked (simplified - in production use a likes table)
        // For now, just increment/decrement
        $short->incrementLikes();

        return response()->json([
            'success' => true,
            'data' => [
                'likes_count' => $short->likes_count,
                'is_liked' => true,
            ],
        ]);
    }

    /**
     * Delete short
     */
    public function delete(Request $request)
    {
        $request->validate([
            'short_id' => 'required|exists:user_shorts,id',
        ]);

        $short = UserShort::where('id', $request->short_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Delete files from storage
        if ($short->video_url) {
            $videoPath = str_replace('/storage/', '', $short->video_url);
            Storage::disk('public')->delete($videoPath);
        }

        if ($short->cover_image_url) {
            $coverPath = str_replace('/storage/', '', $short->cover_image_url);
            Storage::disk('public')->delete($coverPath);
        }

        $short->delete();

        return response()->json([
            'success' => true,
            'message' => 'Short deleted successfully',
        ]);
    }
}
