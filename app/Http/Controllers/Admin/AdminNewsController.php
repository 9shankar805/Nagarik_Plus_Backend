<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminNewsController extends Controller
{
    public function index()
    {
        $news = News::latest()->paginate(20);
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'title_np'     => 'nullable|string|max:255',
            'content'      => 'required|string',
            'content_np'   => 'nullable|string',
            'category'     => 'required|in:notice,service,exam,deadline,update',
            'source'       => 'required|string|max:255',
            'source_url'   => 'nullable|url',
            'image_url'    => 'nullable|url',
            'image_file'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5000',
            'is_featured'  => 'boolean',
            'is_verified'  => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'expires_at'   => 'nullable|date',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('news', 'public');
            $data['image_url'] = Storage::url($path);
        }

        $data['is_featured']  = $request->boolean('is_featured');
        $data['is_verified']  = $request->boolean('is_verified');
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['published_at'] ?? now();

        News::create($data);
        return redirect()->route('admin.news.index')->with('success', 'News article created successfully.');
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'title_np'     => 'nullable|string|max:255',
            'content'      => 'required|string',
            'content_np'   => 'nullable|string',
            'category'     => 'required|in:notice,service,exam,deadline,update',
            'source'       => 'required|string|max:255',
            'source_url'   => 'nullable|url',
            'image_url'    => 'nullable|url',
            'image_file'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5000',
            'is_featured'  => 'boolean',
            'is_verified'  => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'expires_at'   => 'nullable|date',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('news', 'public');
            $data['image_url'] = Storage::url($path);
        }

        $data['is_featured']  = $request->boolean('is_featured');
        $data['is_verified']  = $request->boolean('is_verified');
        $data['is_published'] = $request->boolean('is_published');

        $news->update($data);
        return redirect()->route('admin.news.index')->with('success', 'News article updated successfully.');
    }

    public function destroy(News $news)
    {
        $news->delete();
        return back()->with('success', 'News article deleted successfully.');
    }
}
