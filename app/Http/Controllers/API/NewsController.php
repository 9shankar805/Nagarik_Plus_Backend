<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsLike;
use App\Models\NewsBookmark;
use App\Models\NewsComment;
use App\Models\NewsShare;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = News::published()->withCount(['likes', 'bookmarks', 'comments', 'shares'])->orderByDesc('published_at');

        if ($request->category) {
            $query->where('category', $request->category);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('content', 'like', "%{$request->search}%");
            });
        }
        if ($request->featured) {
            $query->featured();
        }

        $user = Auth::user();

        $news = $query->paginate(15)->through(fn($n) => [
            'id'           => $n->id,
            'title'        => $n->title,
            'title_np'     => $n->title_np,
            'category'     => $n->category,
            'source'       => $n->source,
            'source_url'   => $n->source_url,
            'image_url'    => $n->image_url,
            'images'       => $n->images,
            'video_url'    => $n->video_url,
            'video_thumbnail' => $n->video_thumbnail,
            'media_type'   => $n->media_type,
            'is_verified'  => $n->is_verified,
            'is_featured'  => $n->is_featured,
            'published_at' => $n->published_at?->toDateTimeString(),
            'like_count'   => $n->likes_count,
            'comment_count' => $n->comments_count,
            'share_count'   => $n->shares_count,
            'is_liked'      => $user ? $n->isLikedBy($user->id) : false,
            'is_bookmarked' => $user ? $n->isBookmarkedBy($user->id) : false,
        ]);

        return response()->json(['success' => true, 'data' => $news]);
    }

    public function show(int $id): JsonResponse
    {
        $news = News::published()->withCount(['likes', 'bookmarks', 'comments', 'shares'])->findOrFail($id);
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'           => $news->id,
                'title'        => $news->title,
                'title_np'     => $news->title_np,
                'content'      => $news->content,
                'content_np'   => $news->content_np,
                'category'     => $news->category,
                'source'       => $news->source,
                'source_url'   => $news->source_url,
                'image_url'    => $news->image_url,
                'images'       => $news->images,
                'video_url'    => $news->video_url,
                'video_thumbnail' => $news->video_thumbnail,
                'media_type'   => $news->media_type,
                'is_verified'  => $news->is_verified,
                'published_at' => $news->published_at?->toDateTimeString(),
                'like_count'   => $news->likes_count,
                'comment_count' => $news->comments_count,
                'share_count'   => $news->shares_count,
                'is_liked'      => $user ? $news->isLikedBy($user->id) : false,
                'is_bookmarked' => $user ? $news->isBookmarkedBy($user->id) : false,
            ],
        ]);
    }

    public function categories(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => ['notice', 'service', 'exam', 'deadline', 'update'],
        ]);
    }

    public function shorts(Request $request): JsonResponse
    {
        $query = News::published()->shorts()->withCount(['likes', 'comments', 'shares'])->orderByDesc('published_at');
        $user = Auth::user();

        $shorts = $query->paginate(10)->through(fn($n) => [
            'id'           => $n->id,
            'title'        => $n->title,
            'title_np'     => $n->title_np,
            'image_url'    => $n->image_url,
            'video_url'    => $n->video_url,
            'video_thumbnail' => $n->video_thumbnail,
            'media_type'   => $n->media_type,
            'like_count'   => $n->likes_count,
            'comment_count' => $n->comments_count,
            'share_count'   => $n->shares_count,
            'is_liked'      => $user ? $n->isLikedBy($user->id) : false,
            'is_bookmarked' => $user ? $n->isBookmarkedBy($user->id) : false,
        ]);

        return response()->json(['success' => true, 'data' => $shorts]);
    }

    public function toggleLike(int $id): JsonResponse
    {
        $user = Auth::user();
        $news = News::published()->findOrFail($id);

        $like = NewsLike::where('user_id', $user->id)->where('news_id', $news->id)->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            NewsLike::create(['user_id' => $user->id, 'news_id' => $news->id]);
            $liked = true;
        }

        $news->loadCount('likes');
        return response()->json(['success' => true, 'data' => ['is_liked' => $liked, 'like_count' => $news->likes_count]]);
    }

    public function toggleBookmark(int $id): JsonResponse
    {
        $user = Auth::user();
        $news = News::published()->findOrFail($id);

        $bookmark = NewsBookmark::where('user_id', $user->id)->where('news_id', $news->id)->first();

        if ($bookmark) {
            $bookmark->delete();
            $bookmarked = false;
        } else {
            NewsBookmark::create(['user_id' => $user->id, 'news_id' => $news->id]);
            $bookmarked = true;
        }

        $news->loadCount('bookmarks');
        return response()->json(['success' => true, 'data' => ['is_bookmarked' => $bookmarked, 'bookmark_count' => $news->bookmarks_count]]);
    }

    public function getBookmarks(): JsonResponse
    {
        $user = Auth::user();
        $bookmarks = NewsBookmark::where('user_id', $user->id)->with('news')->orderByDesc('created_at')->paginate(15);

        $data = $bookmarks->through(fn($b) => [
            'id' => $b->news->id,
            'title' => $b->news->title,
            'title_np' => $b->news->title_np,
            'image_url' => $b->news->image_url,
            'created_at' => $b->created_at->toDateTimeString(),
        ]);

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getComments(int $id): JsonResponse
    {
        $news = News::published()->findOrFail($id);
        $comments = NewsComment::where('news_id', $news->id)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $comments]);
    }

    public function addComment(Request $request, int $id): JsonResponse
    {
        $request->validate(['content' => 'required|string']);

        $user = Auth::user();
        $news = News::published()->findOrFail($id);

        $comment = NewsComment::create([
            'user_id' => $user->id,
            'news_id' => $news->id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);

        $comment->load('user');
        $news->loadCount('comments');
        return response()->json(['success' => true, 'data' => ['comment' => $comment, 'comment_count' => $news->comments_count]]);
    }

    public function deleteComment(int $commentId): JsonResponse
    {
        $user = Auth::user();
        $comment = NewsComment::where('user_id', $user->id)->findOrFail($commentId);
        $news = $comment->news;
        $comment->delete();
        $news->loadCount('comments');
        return response()->json(['success' => true, 'data' => ['comment_count' => $news->comments_count]]);
    }

    public function share(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        $news = News::published()->findOrFail($id);

        NewsShare::create([
            'user_id' => $user->id,
            'news_id' => $news->id,
            'platform' => $request->platform,
        ]);

        $news->loadCount('shares');
        return response()->json(['success' => true, 'data' => ['share_count' => $news->shares_count]]);
    }

    /*
    |--------------------------------------------------------------------------
    | User submissions (require auth: sanctum)
    |--------------------------------------------------------------------------
    |  All submissions default to status = PENDING and won't appear to other
    |  users until an admin approves them. Users see their own posts with
    |  status / rejection info via /news/my-submissions.
    */

    public function submitNews(Request $request): JsonResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'title_np'   => 'nullable|string|max:255',
            'content'    => 'required|string|min:20',
            'content_np' => 'nullable|string',
            'category'   => 'required|in:notice,service,exam,deadline,update',
            'source'     => 'nullable|string|max:255',
            'source_url' => 'nullable|url|max:500',
            'image_url'  => 'nullable|url|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5000',
            'images'     => 'nullable|array',
            'images.*'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5000',
            'video_file' => 'nullable|mimes:mp4,mov,avi,webm|max:102400',
            'video_url'  => 'nullable|url',
            'video_thumbnail' => 'nullable|url',
            'video_thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5000',
            'media_type' => 'nullable|in:none,image,video,mixed',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('news', 'public');
            $data['image_url'] = Storage::url($path);
        }

        $imageUrls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('news', 'public');
                $imageUrls[] = Storage::url($path);
            }
            $data['images'] = $imageUrls;
        }

        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('news/videos', 'public');
            $data['video_url'] = Storage::url($path);
            $data['media_type'] = 'video';
        } elseif (!empty($data['video_url'])) {
            $data['media_type'] = 'video';
        }

        if ($request->hasFile('video_thumbnail_file')) {
            $path = $request->file('video_thumbnail_file')->store('news/thumbnails', 'public');
            $data['video_thumbnail'] = Storage::url($path);
        }

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

        $news = News::create([
            'user_id'         => $user->id,
            'title'           => $data['title'],
            'title_np'        => $data['title_np'] ?? null,
            'content'         => $data['content'],
            'content_np'      => $data['content_np'] ?? null,
            'category'        => $data['category'],
            'source'          => ($data['source'] ?? null) ?: ($user->display_name ?? $user->name ?? 'User Submission'),
            'source_url'      => $data['source_url'] ?? null,
            'image_url'       => $data['image_url'] ?? null,
            'images'          => $data['images'] ?? null,
            'video_url'       => $data['video_url'] ?? null,
            'video_thumbnail' => $data['video_thumbnail'] ?? null,
            'media_type'      => $data['media_type'] ?? 'none',
            'status'          => News::STATUS_PENDING,
            'is_short'        => false,
            'is_published'    => false,
            'is_verified'     => false,
            'is_featured'     => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your news has been submitted for admin approval. You will see the status update here once reviewed.',
            'data' => ['submission' => $this->serializeMy($news)],
        ], 201);
    }

    public function submitShort(Request $request): JsonResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'title'      => 'required|string|max:140',
            'title_np'   => 'nullable|string|max:140',
            'category'   => 'required|in:notice,service,exam,deadline,update',
            'image_url'  => 'nullable|url|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5000',
            'video_url'  => 'nullable|url',
            'video_file' => 'nullable|mimes:mp4,mov,avi,webm|max:102400',
            'source'     => 'nullable|string|max:255',
            'source_url' => 'nullable|url|max:500',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('news/shorts', 'public');
            $data['image_url'] = Storage::url($path);
        }

        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('news/shorts/videos', 'public');
            $data['video_url'] = Storage::url($path);
            $data['media_type'] = 'video';
        } elseif (!empty($data['video_url'])) {
            $data['media_type'] = 'video';
        } else {
            $data['media_type'] = 'image';
        }

        if (empty($data['image_url']) && empty($data['video_url'])) {
            return response()->json(['success' => false, 'message' => 'An image or video is required for shorts.'], 422);
        }

        $news = News::create([
            'user_id'      => $user->id,
            'title'        => $data['title'],
            'title_np'     => $data['title_np'] ?? null,
            'content'      => $data['title'],
            'category'     => $data['category'],
            'source'       => ($data['source'] ?? null) ?: ($user->display_name ?? $user->name ?? 'User Submission'),
            'source_url'   => $data['source_url'] ?? null,
            'image_url'    => $data['image_url'] ?? null,
            'video_url'    => $data['video_url'] ?? null,
            'media_type'   => $data['media_type'] ?? 'image',
            'status'       => News::STATUS_PENDING,
            'is_short'     => true,
            'is_published' => false,
            'is_verified'  => false,
            'is_featured'  => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your short has been submitted for admin approval. It will appear in the shorts feed once approved.',
            'data' => ['submission' => $this->serializeMy($news)],
        ], 201);
    }

    public function mySubmissions(Request $request): JsonResponse
    {
        $user = Auth::user();

        $rows = News::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate((int) $request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => [
                'items'      => $rows->through(fn ($n) => $this->serializeMy($n))->items(),
                'pagination' => [
                    'total'        => $rows->total(),
                    'per_page'     => $rows->perPage(),
                    'current_page' => $rows->currentPage(),
                    'last_page'    => $rows->lastPage(),
                ],
            ],
        ]);
    }

    private function serializeMy(News $n): array
    {
        return [
            'id'               => $n->id,
            'title'            => $n->title,
            'title_np'         => $n->title_np,
            'content'          => $n->content,
            'content_np'       => $n->content_np,
            'category'         => $n->category,
            'is_short'         => (bool) $n->is_short,
            'image_url'        => $n->image_url,
            'images'           => $n->images,
            'video_url'        => $n->video_url,
            'video_thumbnail'  => $n->video_thumbnail,
            'media_type'       => $n->media_type,
            'source'           => $n->source,
            'source_url'       => $n->source_url,
            'status'           => $n->status,                     // pending | approved | rejected
            'is_published'     => (bool) $n->is_published,
            'rejection_reason' => $n->rejection_reason,          // only when status === rejected
            'reviewed_at'      => $n->reviewed_at?->toIso8601String(),
            'submitted_at'     => $n->created_at->toIso8601String(),
            'updated_at'       => $n->updated_at->toIso8601String(),
        ];
    }
}
