<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::where('is_published', true);
        if ($request->category) {
            $query->where('category', $request->category);
        }
        $news = $query->latest()->paginate(20);
        $categories = News::select('category')->distinct()->pluck('category');
        return view('user.news', compact('news', 'categories'));
    }
}
