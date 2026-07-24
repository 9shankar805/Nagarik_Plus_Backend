<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Short;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminShortsController extends Controller
{
    public function index()
    {
        $shorts = Short::latest()->paginate(15);
        return view('admin.shorts.index', compact('shorts'));
    }

    public function create()
    {
        return view('admin.shorts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_np' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_np' => 'nullable|string',
            'category' => 'required|string|max:255',
            'video_file' => 'nullable|file|mimes:mp4,mov,avi,mkv,webm|max:50000',
            'video_url_text' => 'nullable|url|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5000',
        ]);

        $videoUrl = $request->video_url_text;
        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('shorts/videos', 'public');
            $videoUrl = Storage::url($path);
        }

        $thumbnailUrl = null;
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('shorts/thumbnails', 'public');
            $thumbnailUrl = Storage::url($path);
        }

        Short::create([
            'title_en' => $validated['title_en'],
            'title_np' => $validated['title_np'],
            'description_en' => $validated['description_en'],
            'description_np' => $validated['description_np'],
            'category' => $validated['category'],
            'video_url' => $videoUrl ?? 'https://www.w3schools.com/html/mov_bbb.mp4',
            'thumbnail_url' => $thumbnailUrl,
            'is_published' => $request->has('is_published'),
        ]);

        return redirect()->route('admin.shorts.index')->with('success', 'Educational video short uploaded successfully.');
    }

    public function edit(Short $short)
    {
        return view('admin.shorts.edit', compact('short'));
    }

    public function update(Request $request, Short $short)
    {
        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_np' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_np' => 'nullable|string',
            'category' => 'required|string|max:255',
            'video_file' => 'nullable|file|mimes:mp4,mov,avi,mkv,webm|max:50000',
            'video_url_text' => 'nullable|url|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5000',
        ]);

        $videoUrl = $short->video_url;
        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('shorts/videos', 'public');
            $videoUrl = Storage::url($path);
        } elseif ($request->filled('video_url_text')) {
            $videoUrl = $request->video_url_text;
        }

        $thumbnailUrl = $short->thumbnail_url;
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('shorts/thumbnails', 'public');
            $thumbnailUrl = Storage::url($path);
        }

        $short->update([
            'title_en' => $validated['title_en'],
            'title_np' => $validated['title_np'],
            'description_en' => $validated['description_en'],
            'description_np' => $validated['description_np'],
            'category' => $validated['category'],
            'video_url' => $videoUrl,
            'thumbnail_url' => $thumbnailUrl,
            'is_published' => $request->has('is_published'),
        ]);

        return redirect()->route('admin.shorts.index')->with('success', 'Educational video short updated successfully.');
    }

    public function destroy(Short $short)
    {
        $short->delete();
        return redirect()->route('admin.shorts.index')->with('success', 'Video short deleted successfully.');
    }
}
