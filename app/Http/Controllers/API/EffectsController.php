<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Effect;
use App\Models\EffectCategory;
use Illuminate\Http\Request;

class EffectsController extends Controller
{
    /**
     * Get effect categories
     */
    public function categories()
    {
        $categories = EffectCategory::ordered()->get();
        
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
     * Get effects by category
     */
    public function list(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:effect_categories,id',
        ]);

        $effects = Effect::byCategory($request->category_id)->limit(20)->get();
        
        return response()->json([
            'success' => true,
            'data' => $effects->map(function ($effect) {
                return [
                    'id' => $effect->id,
                    'title' => $effect->title,
                    'thumbnail_url' => $effect->thumbnail_url,
                    'deepar_file_url' => $effect->deepar_file_url,
                    'file_size_kb' => $effect->file_size_kb,
                ];
            }),
        ]);
    }

    /**
     * Get trending effects
     */
    public function trending()
    {
        $trendingEffects = Effect::trending()->limit(20)->get();
        
        return response()->json([
            'success' => true,
            'data' => $trendingEffects->map(function ($effect) {
                return [
                    'id' => $effect->id,
                    'title' => $effect->title,
                    'thumbnail_url' => $effect->thumbnail_url,
                    'deepar_file_url' => $effect->deepar_file_url,
                    'file_size_kb' => $effect->file_size_kb,
                ];
            }),
        ]);
    }
}
