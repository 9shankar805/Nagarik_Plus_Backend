<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningCategory;
use App\Models\VideoClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminVideoClassController extends Controller
{
    public function index(Request $request)
    {
        $query = VideoClass::with('category:id,name_en,icon');

        if ($request->category_id) {
            $query->where('learning_category_id', $request->category_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->type) {
            $query->where('is_live', $request->type === 'live');
        }
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $videos     = $query->latest()->paginate(20)->withQueryString();
        $categories = LearningCategory::active()->ordered()->get();

        return view('admin.learning.video-classes.index', compact('videos', 'categories'));
    }

    public function create()
    {
        $categories = LearningCategory::active()->ordered()->get();
        return view('admin.learning.video-classes.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'learning_category_id' => 'nullable|exists:learning_categories,id',
            'title'                => 'required|string|max:255',
            'description'          => 'nullable|string',
            'video_url'            => 'required|url|max:500',
            'duration_minutes'     => 'nullable|integer|min:1|max:600',
            'is_live'              => 'nullable|boolean',
            'live_scheduled_at'    => 'nullable|date',
            'thumbnail_file'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $thumbnailUrl = null;
        if ($request->hasFile('thumbnail_file')) {
            $path         = $request->file('thumbnail_file')->store('learning/video-classes', 'public');
            $thumbnailUrl = Storage::url($path);
        }

        VideoClass::create([
            'learning_category_id' => $request->learning_category_id ?: null,
            'title'                => $request->title,
            'description'          => $request->description,
            'video_url'            => $request->video_url,
            'thumbnail_url'        => $thumbnailUrl,
            'duration_minutes'     => $request->duration_minutes,
            'is_live'              => $request->boolean('is_live'),
            'live_scheduled_at'    => $request->live_scheduled_at,
            'status'               => $request->input('status', 'active'),
        ]);

        return redirect()->route('admin.learning.video-classes.index')
            ->with('success', 'Video class created successfully.');
    }

    public function edit(VideoClass $videoClass)
    {
        $categories = LearningCategory::active()->ordered()->get();
        return view('admin.learning.video-classes.edit', compact('videoClass', 'categories'));
    }

    public function update(Request $request, VideoClass $videoClass)
    {
        $request->validate([
            'learning_category_id' => 'nullable|exists:learning_categories,id',
            'title'                => 'required|string|max:255',
            'description'          => 'nullable|string',
            'video_url'            => 'required|url|max:500',
            'duration_minutes'     => 'nullable|integer|min:1|max:600',
            'is_live'              => 'nullable|boolean',
            'live_scheduled_at'    => 'nullable|date',
            'thumbnail_file'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $thumbnailUrl = $videoClass->thumbnail_url;
        if ($request->hasFile('thumbnail_file')) {
            $path         = $request->file('thumbnail_file')->store('learning/video-classes', 'public');
            $thumbnailUrl = Storage::url($path);
        }

        $videoClass->update([
            'learning_category_id' => $request->learning_category_id ?: null,
            'title'                => $request->title,
            'description'          => $request->description,
            'video_url'            => $request->video_url,
            'thumbnail_url'        => $thumbnailUrl,
            'duration_minutes'     => $request->duration_minutes,
            'is_live'              => $request->boolean('is_live'),
            'live_scheduled_at'    => $request->live_scheduled_at,
            'status'               => $request->input('status', 'active'),
        ]);

        return redirect()->route('admin.learning.video-classes.index')
            ->with('success', 'Video class updated successfully.');
    }

    public function destroy(VideoClass $videoClass)
    {
        $videoClass->delete();
        return back()->with('success', 'Video class deleted.');
    }
}
