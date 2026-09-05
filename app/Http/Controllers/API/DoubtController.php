<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Doubt;
use App\Models\DoubtAnswer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoubtController extends Controller
{
    /**
     * GET /api/v1/doubts
     * Paginated list of public doubts (all statuses visible to community).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Doubt::with(['user:id,name', 'category:id,name_en,name_np'])
            ->withCount('answers');

        if ($request->filled('category_id')) {
            $query->where('learning_category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $doubts = $query->orderByDesc('updated_at')->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $doubts,
        ]);
    }

    /**
     * POST /api/v1/doubts  [auth]
     * Submit a new doubt.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'learning_category_id' => 'nullable|exists:learning_categories,id',
            'subject'              => 'required|string|max:255',
            'question_text'        => 'required|string|max:2000',
            'image_url'            => 'nullable|url',
        ]);

        $doubt = Doubt::create([
            'user_id'              => $request->user()->id,
            'learning_category_id' => $request->learning_category_id,
            'subject'              => $request->subject,
            'question_text'        => $request->question_text,
            'image_url'            => $request->image_url,
            'status'               => Doubt::STATUS_OPEN,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your doubt has been submitted.',
            'data'    => $doubt,
        ], 201);
    }

    /**
     * GET /api/v1/doubts/{id}
     * Detail view of a single doubt including all answers.
     */
    public function show(int $id): JsonResponse
    {
        $doubt = Doubt::with([
            'user:id,name',
            'category:id,name_en,name_np',
            'answers.user:id,name',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $doubt,
        ]);
    }

    /**
     * DELETE /api/v1/doubts/{id}  [auth]
     * Delete the authenticated user's own doubt (only if pending/open).
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $doubt = Doubt::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($doubt->status === Doubt::STATUS_ANSWERED) {
            return response()->json([
                'success' => false,
                'message' => 'Answered doubts cannot be deleted.',
            ], 422);
        }

        $doubt->delete();

        return response()->json([
            'success' => true,
            'message' => 'Doubt deleted.',
        ]);
    }

    /**
     * POST /api/v1/doubts/{id}/answers  [auth]
     * Post an answer to a doubt.
     */
    public function answer(Request $request, int $id): JsonResponse
    {
        $doubt = Doubt::findOrFail($id);

        $request->validate([
            'body'      => 'required|string|max:3000',
            'image_url' => 'nullable|url',
        ]);

        $answer = DoubtAnswer::create([
            'doubt_id'  => $doubt->id,
            'user_id'   => $request->user()->id,
            'body'      => $request->body,
            'image_url' => $request->image_url,
        ]);

        // Mark doubt as answered if it was open
        if ($doubt->status === Doubt::STATUS_OPEN) {
            $doubt->update(['status' => Doubt::STATUS_ANSWERED]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Answer posted.',
            'data'    => $answer->load('user:id,name'),
        ], 201);
    }

    /**
     * POST /api/v1/doubts/{id}/upvote  [auth]
     * Toggle an upvote on a doubt.
     */
    public function upvote(Request $request, int $id): JsonResponse
    {
        $doubt = Doubt::findOrFail($id);

        // Simple increment — no separate pivot table; just bump the counter.
        // For idempotent toggle behavior, we track via a JSON set in session cache.
        $cacheKey = "doubt_upvote_{$id}_user_{$request->user()->id}";

        if (cache()->has($cacheKey)) {
            // Already upvoted — remove upvote
            $doubt->decrement('upvotes');
            cache()->forget($cacheKey);
            $upvoted = false;
        } else {
            $doubt->increment('upvotes');
            cache()->put($cacheKey, true, now()->addDays(30));
            $upvoted = true;
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'upvoted'  => $upvoted,
                'upvotes'  => $doubt->fresh()->upvotes,
            ],
        ]);
    }

    /**
     * PATCH /api/v1/doubts/{doubtId}/answers/{answerId}/accept  [auth]
     * Mark an answer as the accepted answer (only the doubt owner can do this).
     */
    public function acceptAnswer(Request $request, int $doubtId, int $answerId): JsonResponse
    {
        $doubt = Doubt::where('id', $doubtId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $answer = DoubtAnswer::where('id', $answerId)
            ->where('doubt_id', $doubtId)
            ->firstOrFail();

        // Unaccept any previously accepted answer
        DoubtAnswer::where('doubt_id', $doubtId)
            ->where('is_accepted', true)
            ->update(['is_accepted' => false]);

        $answer->update(['is_accepted' => true]);
        $doubt->update(['status' => Doubt::STATUS_ANSWERED]);

        return response()->json([
            'success' => true,
            'message' => 'Answer marked as accepted.',
            'data'    => $answer,
        ]);
    }
}
