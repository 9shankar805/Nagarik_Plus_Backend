<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Audio;
use App\Models\AudioCategory;
use Illuminate\Http\Request;

class AudioController extends Controller
{
    /**
     * Get trending audio
     */
    public function trending()
    {
        $trendingAudio = Audio::trending()->limit(20)->get();
        
        return response()->json([
            'success' => true,
            'data' => $trendingAudio->map(function ($audio) {
                return [
                    'id' => $audio->id,
                    'title' => $audio->title,
                    'artist' => $audio->artist,
                    'duration_seconds' => $audio->duration_seconds,
                    'cover_image_url' => $audio->cover_image_url,
                    'audio_url' => $audio->audio_url,
                    'usage_count' => $audio->usage_count,
                ];
            }),
        ]);
    }

    /**
     * Get audio categories
     */
    public function categories()
    {
        $categories = AudioCategory::ordered()->get();
        
        return response()->json([
            'success' => true,
            'data' => $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'icon' => $category->icon,
                ];
            }),
        ]);
    }

    /**
     * Search audio
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $searchQuery = $request->q;
        $results = Audio::search($searchQuery)->limit(20)->get();
        
        return response()->json([
            'success' => true,
            'data' => $results->map(function ($audio) {
                return [
                    'id' => $audio->id,
                    'title' => $audio->title,
                    'artist' => $audio->artist,
                    'duration_seconds' => $audio->duration_seconds,
                    'cover_image_url' => $audio->cover_image_url,
                    'audio_url' => $audio->audio_url,
                    'usage_count' => $audio->usage_count,
                ];
            }),
        ]);
    }

    /**
     * Get audio by category
     */
    public function byCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:audio_categories,id',
        ]);

        $audio = Audio::byCategory($request->category_id)->limit(20)->get();
        
        return response()->json([
            'success' => true,
            'data' => $audio->map(function ($audio) {
                return [
                    'id' => $audio->id,
                    'title' => $audio->title,
                    'artist' => $audio->artist,
                    'duration_seconds' => $audio->duration_seconds,
                    'cover_image_url' => $audio->cover_image_url,
                    'audio_url' => $audio->audio_url,
                    'usage_count' => $audio->usage_count,
                ];
            }),
        ]);
    }
}
