<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChapterRating;
use App\Models\LearningCategory;
use App\Models\LearningChapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminChapterRatingController extends Controller
{
    /** Index: all ratings with filters */
    public function index(Request $request)
    {
        $query = ChapterRating::with([
            'user:id,name,email',
            'chapter:id,title_en,learning_category_id',
            'chapter.category:id,name_en,icon',
        ]);

        if ($request->category_id) {
            $query->whereHas('chapter', fn($q) =>
                $q->where('learning_category_id', $request->category_id)
            );
        }
        if ($request->chapter_id) {
            $query->where('learning_chapter_id', $request->chapter_id);
        }
        if ($request->rating) {
            $query->where('rating', $request->rating);
        }

        $ratings    = $query->latest()->paginate(25)->withQueryString();
        $categories = LearningCategory::active()->ordered()->get();

        // Chapter list for filter (depends on category selection)
        $chapters = $request->category_id
            ? LearningChapter::where('learning_category_id', $request->category_id)
                ->select('id', 'title_en')
                ->orderBy('display_order')
                ->get()
            : collect();

        // Overall stats
        $avgRating   = ChapterRating::avg('rating') ?? 0;
        $totalRatings = ChapterRating::count();
        $dist = ChapterRating::select('rating', DB::raw('count(*) as cnt'))
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->pluck('cnt', 'rating')
            ->toArray();

        return view('admin.learning.chapter-ratings.index', compact(
            'ratings', 'categories', 'chapters', 'avgRating', 'totalRatings', 'dist'
        ));
    }

    /** Per-chapter summary: avg score, rating distribution */
    public function chapterSummary(Request $request)
    {
        $query = LearningChapter::with('category:id,name_en,icon')
            ->withCount('ratings as total_ratings')
            ->withAvg('ratings as avg_rating', 'rating')
            ->having('total_ratings', '>', 0);

        if ($request->category_id) {
            $query->where('learning_category_id', $request->category_id);
        }

        $chapters   = $query->orderByDesc('avg_rating')->paginate(20)->withQueryString();
        $categories = LearningCategory::active()->ordered()->get();

        return view('admin.learning.chapter-ratings.summary', compact('chapters', 'categories'));
    }

    /** Delete a single rating */
    public function destroy(ChapterRating $rating)
    {
        $rating->delete();
        return back()->with('success', 'Rating removed.');
    }
}
