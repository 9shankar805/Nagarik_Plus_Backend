<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DailyQuiz;
use App\Models\Doubt;
use App\Models\SubscriptionPackage;
use App\Models\VideoClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminLearningController extends Controller
{
    // =========================================================================
    // VIDEO CLASSES MANAGEMENT
    // =========================================================================

    public function listVideos(Request $request): JsonResponse
    {
        $videos = VideoClass::with('category')->orderBy('created_at', 'desc')->paginate(20);
        return response()->json(['success' => true, 'data' => $videos]);
    }

    public function storeVideo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'learning_category_id' => 'required|exists:learning_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'thumbnail_url' => 'nullable|url',
            'duration_minutes' => 'nullable|integer',
            'is_live' => 'required|boolean',
            'live_scheduled_at' => 'nullable|date',
            'status' => 'required|in:scheduled,live,recorded,inactive',
        ]);

        $video = VideoClass::create($validated);
        return response()->json(['success' => true, 'data' => $video], 201);
    }

    public function updateVideo(Request $request, $id): JsonResponse
    {
        $video = VideoClass::findOrFail($id);
        $validated = $request->validate([
            'learning_category_id' => 'sometimes|exists:learning_categories,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'thumbnail_url' => 'nullable|url',
            'duration_minutes' => 'nullable|integer',
            'is_live' => 'sometimes|boolean',
            'live_scheduled_at' => 'nullable|date',
            'status' => 'sometimes|in:scheduled,live,recorded,inactive',
        ]);

        $video->update($validated);
        return response()->json(['success' => true, 'data' => $video]);
    }

    public function destroyVideo($id): JsonResponse
    {
        $video = VideoClass::findOrFail($id);
        $video->delete();
        return response()->json(['success' => true, 'message' => 'Video deleted.']);
    }

    // =========================================================================
    // SUBSCRIPTION PACKAGES MANAGEMENT
    // =========================================================================

    public function listSubscriptions(): JsonResponse
    {
        $packages = SubscriptionPackage::orderBy('price', 'asc')->paginate(20);
        return response()->json(['success' => true, 'data' => $packages]);
    }

    public function storeSubscription(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'features_json' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $package = SubscriptionPackage::create($validated);
        return response()->json(['success' => true, 'data' => $package], 201);
    }

    public function updateSubscription(Request $request, $id): JsonResponse
    {
        $package = SubscriptionPackage::findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'duration_days' => 'sometimes|integer|min:1',
            'features_json' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $package->update($validated);
        return response()->json(['success' => true, 'data' => $package]);
    }

    public function destroySubscription($id): JsonResponse
    {
        $package = SubscriptionPackage::findOrFail($id);
        $package->delete();
        return response()->json(['success' => true, 'message' => 'Subscription package deleted.']);
    }

    // =========================================================================
    // DOUBTS / ASK FEATURE MANAGEMENT
    // =========================================================================

    public function listDoubts(Request $request): JsonResponse
    {
        $query = Doubt::with(['user', 'expert', 'category'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json(['success' => true, 'data' => $query->paginate(20)]);
    }

    public function answerDoubt(Request $request, $id): JsonResponse
    {
        $doubt = Doubt::findOrFail($id);
        $request->validate([
            'answer_text' => 'required|string',
        ]);

        $doubt->update([
            'answer_text' => $request->answer_text,
            'expert_id' => $request->user()->id,
            'status' => 'answered',
        ]);

        return response()->json(['success' => true, 'data' => $doubt]);
    }

    // =========================================================================
    // DAILY QUIZ MANAGEMENT
    // =========================================================================

    public function listDailyQuizzes(): JsonResponse
    {
        $quizzes = DailyQuiz::with('question')->orderBy('quiz_date', 'desc')->paginate(20);
        return response()->json(['success' => true, 'data' => $quizzes]);
    }

    public function storeDailyQuiz(Request $request): JsonResponse
    {
        $request->validate([
            'quiz_question_id' => 'required|exists:quiz_questions,id',
            'quiz_date' => 'required|date|unique:daily_quizzes,quiz_date',
        ]);

        $daily = DailyQuiz::create([
            'quiz_question_id' => $request->quiz_question_id,
            'quiz_date' => $request->quiz_date,
        ]);

        return response()->json(['success' => true, 'data' => $daily], 201);
    }
}
