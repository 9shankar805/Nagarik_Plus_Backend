<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminNewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::with(['author:id,name,email,profile_photo']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('type')) {
            if ($request->input('type') === 'short') $query->where('is_short', true);
            elseif ($request->input('type') === 'news') $query->where('is_short', false);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(fn ($q) => $q->where('title', 'like', $search)->orWhere('content', 'like', $search));
        }

        $news = $query->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'rejected' THEN 1 ELSE 2 END")
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'pending'  => News::pending()->count(),
            'approved' => News::approved()->count(),
            'rejected' => News::rejected()->count(),
        ];

        return view('admin.news.index', compact('news', 'counts'));
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
            'images'       => 'nullable|array',
            'images.*'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5000',
            'video_file'   => 'nullable|mimes:mp4,mov,avi,webm|max:102400',
            'video_url'    => 'nullable|url',
            'video_thumbnail' => 'nullable|url',
            'video_thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5000',
            'media_type'   => 'nullable|in:none,image,video,mixed',
            'is_short'     => 'nullable|boolean',
            'is_featured'  => 'boolean',
            'is_verified'  => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'expires_at'   => 'nullable|date',
        ]);

        // Handle single image upload
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('news', 'public');
            $data['image_url'] = Storage::url($path);
        }

        // Handle multiple images upload
        $imageUrls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('news', 'public');
                $imageUrls[] = Storage::url($path);
            }
            $data['images'] = json_encode($imageUrls);
        }

        // Handle video upload
        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('news/videos', 'public');
            $data['video_url'] = Storage::url($path);
            $data['media_type'] = 'video';
        } elseif (!empty($data['video_url'])) {
            $data['media_type'] = 'video';
        }

        // Handle video thumbnail upload
        if ($request->hasFile('video_thumbnail_file')) {
            $path = $request->file('video_thumbnail_file')->store('news/thumbnails', 'public');
            $data['video_thumbnail'] = Storage::url($path);
        }

        // Determine media type
        if (empty($data['media_type']) || $data['media_type'] === 'none') {
            if (!empty($imageUrls) && !empty($data['video_url'])) {
                $data['media_type'] = 'mixed';
            } elseif (!empty($imageUrls)) {
                $data['media_type'] = 'image';
            } elseif (!empty($data['video_url'])) {
                $data['media_type'] = 'video';
            } else {
                $data['media_type'] = 'none';
            }
        }

        $data['is_featured']  = $request->boolean('is_featured');
        $data['is_verified']  = $request->boolean('is_verified', true);
        $data['is_published'] = $request->boolean('is_published', true);
        $data['is_short']     = $request->boolean('is_short', false);
        
        $data['published_at'] = empty($data['published_at']) ? now() : $data['published_at'];
        $data['expires_at']   = empty($data['expires_at']) ? null : $data['expires_at'];
        
        $data['status']       = News::STATUS_APPROVED;
        $data['reviewed_at']  = now();
        $data['reviewed_by']  = $request->user()?->id;

        News::create($data);
        return redirect()->route('admin.news.index')->with('success', 'News article created and published successfully.');
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
            'images'       => 'nullable|array',
            'images.*'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5000',
            'video_file'   => 'nullable|mimes:mp4,mov,avi,webm|max:102400',
            'video_url'    => 'nullable|url',
            'video_thumbnail' => 'nullable|url',
            'video_thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5000',
            'media_type'   => 'nullable|in:none,image,video,mixed',
            'is_short'     => 'nullable|boolean',
            'is_featured'  => 'boolean',
            'is_verified'  => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'expires_at'   => 'nullable|date',
            'status'       => 'nullable|in:pending,approved,rejected',
        ]);

        // Handle single image upload
        if ($request->hasFile('image_file')) {
            // Delete old image if exists
            if ($news->image_url && str_starts_with($news->image_url, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $news->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image_file')->store('news', 'public');
            $data['image_url'] = Storage::url($path);
        }

        // Handle multiple images upload
        $imageUrls = [];
        if ($request->hasFile('images')) {
            // Delete old images if exists
            if ($news->images) {
                $oldImages = json_decode($news->images, true);
                foreach ($oldImages as $oldImage) {
                    if (str_starts_with($oldImage, '/storage/')) {
                        $oldPath = str_replace('/storage/', '', $oldImage);
                        Storage::disk('public')->delete($oldPath);
                    }
                }
            }
            foreach ($request->file('images') as $image) {
                $path = $image->store('news', 'public');
                $imageUrls[] = Storage::url($path);
            }
            $data['images'] = json_encode($imageUrls);
        }

        // Handle video upload
        if ($request->hasFile('video_file')) {
            // Delete old video if exists
            if ($news->video_url && str_starts_with($news->video_url, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $news->video_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('video_file')->store('news/videos', 'public');
            $data['video_url'] = Storage::url($path);
            $data['media_type'] = 'video';
        } elseif (!empty($data['video_url'])) {
            $data['media_type'] = 'video';
        }

        // Handle video thumbnail upload
        if ($request->hasFile('video_thumbnail_file')) {
            // Delete old thumbnail if exists
            if ($news->video_thumbnail && str_starts_with($news->video_thumbnail, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $news->video_thumbnail);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('video_thumbnail_file')->store('news/thumbnails', 'public');
            $data['video_thumbnail'] = Storage::url($path);
        }

        // Determine media type
        if (empty($data['media_type']) || $data['media_type'] === 'none') {
            if (!empty($imageUrls) && !empty($data['video_url'])) {
                $data['media_type'] = 'mixed';
            } elseif (!empty($imageUrls)) {
                $data['media_type'] = 'image';
            } elseif (!empty($data['video_url'])) {
                $data['media_type'] = 'video';
            } else {
                $data['media_type'] = 'none';
            }
        }

        $data['is_featured']  = $request->boolean('is_featured');
        $data['is_verified']  = $request->boolean('is_verified');
        $data['is_published'] = $request->boolean('is_published');
        $data['is_short']     = $request->boolean('is_short', $news->is_short);

        if (isset($data['status']) && $data['status'] !== $news->status) {
            // status change via edit — also clear/set audit fields
            if ($data['status'] === News::STATUS_APPROVED) {
                $data['is_published']     = true;
                $data['rejection_reason'] = null;
                $data['reviewed_at']      = now();
                $data['reviewed_by']      = $request->user()?->id;
                $data['published_at']     = $news->published_at ?? now();
            } elseif ($data['status'] === News::STATUS_PENDING) {
                $data['rejection_reason'] = null;
                $data['reviewed_at']      = null;
                $data['reviewed_by']      = null;
            }
        }
        
        $data['published_at'] = empty($data['published_at']) ? null : $data['published_at'];
        $data['expires_at']   = empty($data['expires_at']) ? null : $data['expires_at'];

        $news->update($data);
        return redirect()->route('admin.news.index')->with('success', 'News article updated successfully.');
    }

    public function destroy(News $news)
    {
        $news->delete();
        return back()->with('success', 'News article deleted successfully.');
    }

    public function approve(Request $request, News $news)
    {
        $admin = $request->user()?->id;
        $news->approve($admin);

        return back()->with('success', ($news->is_short ? 'Short' : 'News article') . ' #' . $news->id . ' approved and published.');
    }

    public function reject(Request $request, News $news)
    {
        $data = $request->validate([
            'reason' => 'required|string|min:5|max:1000',
        ]);
        $admin = $request->user()?->id;
        $news->reject($data['reason'], $admin);

        return back()->with('success', ($news->is_short ? 'Short' : 'News article') . ' #' . $news->id . ' rejected. Author will see the reason.');
    }

    public function sendBack(Request $request, News $news)
    {
        $news->sendBackToPending();
        return back()->with('success', ($news->is_short ? 'Short' : 'News article') . ' #' . $news->id . ' sent back to pending.');
    }
}
