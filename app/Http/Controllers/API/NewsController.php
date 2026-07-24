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
}
