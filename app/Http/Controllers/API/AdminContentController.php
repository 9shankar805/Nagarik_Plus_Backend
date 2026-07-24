<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CitizenService;
use App\Models\EmergencyContact;
use App\Models\News;
use App\Models\Office;
use App\Models\QuizQuestion;
use App\Models\RoadSign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminContentController extends Controller
{
    // =========================================================================
    // News
    // =========================================================================

    public function listNews(Request $request): JsonResponse
    {
        $query = News::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->has('is_published')) {
            $query->where('is_published', (bool) $request->input('is_published'));
        }

        $news = $query->orderByDesc('created_at')->paginate(20)->through(fn ($n) => [
            'id'           => $n->id,
            'title'        => $n->title,
            'title_np'     => $n->title_np,
            'category'     => $n->category,
            'source'       => $n->source,
            'is_published' => $n->is_published,
            'is_featured'  => $n->is_featured,
            'is_verified'  => $n->is_verified,
            'published_at' => $n->published_at?->toDateTimeString(),
            'created_at'   => $n->created_at->toDateTimeString(),
        ]);

        return response()->json(['success' => true, 'data' => $news]);
    }

    public function storeNews(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'        => 'required|string|max:500',
            'title_np'     => 'nullable|string|max:500',
            'content'      => 'required|string',
            'content_np'   => 'nullable|string',
            'category'     => 'required|string|max:100',
            'source'       => 'nullable|string|max:255',
            'source_url'   => 'nullable|url|max:500',
            'image_url'    => 'nullable|url|max:500',
            'is_verified'  => 'boolean',
            'is_featured'  => 'boolean',
            'is_published' => 'boolean',
            'expires_at'   => 'nullable|date',
        ]);

        if (!empty($data['is_published']) && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $news = News::create($data);

        return response()->json([
            'success' => true,
            'message' => 'News created successfully.',
            'data'    => $news,
        ], 201);
    }

    public function updateNews(Request $request, int $id): JsonResponse
    {
        $news = News::findOrFail($id);

        $data = $request->validate([
            'title'        => 'sometimes|string|max:500',
            'title_np'     => 'nullable|string|max:500',
            'content'      => 'sometimes|string',
            'content_np'   => 'nullable|string',
            'category'     => 'sometimes|string|max:100',
            'source'       => 'nullable|string|max:255',
            'source_url'   => 'nullable|url|max:500',
            'image_url'    => 'nullable|url|max:500',
            'is_verified'  => 'boolean',
            'is_featured'  => 'boolean',
            'is_published' => 'boolean',
            'expires_at'   => 'nullable|date',
        ]);

        $shouldBroadcast = false;
        // Set published_at when is_published transitions to true
        if (isset($data['is_published']) && $data['is_published'] && !$news->is_published) {
            $data['published_at'] = now();
            $shouldBroadcast = true;
        }

        $news->update($data);

        if ($shouldBroadcast) {
            \App\Jobs\BroadcastNewsJob::dispatch($news->fresh());
        }

        return response()->json([
            'success' => true,
            'message' => 'News updated successfully.',
            'data'    => $news->fresh(),
        ]);
    }

    public function publishNews(Request $request, int $id): JsonResponse
    {
        $news = News::findOrFail($id);

        $news->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        \App\Jobs\BroadcastNewsJob::dispatch($news->fresh());

        return response()->json([
            'success' => true,
            'message' => 'News published and broadcast initiated.',
        ]);
    }

    public function destroyNews(int $id): JsonResponse
    {
        $news = News::findOrFail($id);
        $news->delete();

        return response()->json([
            'success' => true,
            'message' => 'News deleted successfully.',
        ]);
    }

    // =========================================================================
    // Citizen Services
    // =========================================================================

    public function listServices(Request $request): JsonResponse
    {
        $query = CitizenService::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', (bool) $request->input('is_active'));
        }

        $services = $query->orderBy('sort_order')->paginate(20);

        return response()->json(['success' => true, 'data' => $services]);
    }

    public function storeService(Request $request): JsonResponse
    {
        $data = $request->validate([
            'slug'                 => 'required|string|max:255|unique:citizen_services,slug',
            'title'                => 'required|string|max:255',
            'title_np'             => 'nullable|string|max:255',
            'description'          => 'nullable|string',
            'description_np'       => 'nullable|string',
            'category'             => 'required|string|max:100',
            'icon'                 => 'nullable|string|max:100',
            'color'                => 'nullable|string|max:20',
            'eligibility'          => 'nullable|string',
            'required_documents'   => 'nullable|array',
            'application_steps'    => 'nullable|array',
            'fee'                  => 'nullable|string|max:255',
            'processing_time'      => 'nullable|string|max:255',
            'faqs'                 => 'nullable|array',
            'official_url'         => 'nullable|url|max:500',
            'sort_order'           => 'nullable|integer',
            'is_active'            => 'boolean',
        ]);

        $service = CitizenService::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully.',
            'data'    => $service,
        ], 201);
    }

    public function updateService(Request $request, int $id): JsonResponse
    {
        $service = CitizenService::findOrFail($id);

        $data = $request->validate([
            'slug'                 => 'sometimes|string|max:255|unique:citizen_services,slug,' . $id,
            'title'                => 'sometimes|string|max:255',
            'title_np'             => 'nullable|string|max:255',
            'description'          => 'nullable|string',
            'description_np'       => 'nullable|string',
            'category'             => 'sometimes|string|max:100',
            'icon'                 => 'nullable|string|max:100',
            'color'                => 'nullable|string|max:20',
            'eligibility'          => 'nullable|string',
            'required_documents'   => 'nullable|array',
            'application_steps'    => 'nullable|array',
            'fee'                  => 'nullable|string|max:255',
            'processing_time'      => 'nullable|string|max:255',
            'faqs'                 => 'nullable|array',
            'official_url'         => 'nullable|url|max:500',
            'sort_order'           => 'nullable|integer',
            'is_active'            => 'boolean',
        ]);

        $service->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully.',
            'data'    => $service->fresh(),
        ]);
    }

    public function destroyService(int $id): JsonResponse
    {
        CitizenService::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully.',
        ]);
    }

    // =========================================================================
    // Quiz Questions
    // =========================================================================

    public function listQuestions(Request $request): JsonResponse
    {
        $query = QuizQuestion::withoutGlobalScopes();

        if ($search = $request->input('search')) {
            $query->where('question', 'like', "%{$search}%");
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        if ($difficulty = $request->input('difficulty')) {
            $query->where('difficulty', $difficulty);
        }

        // Admin sees hidden fields (correct_index, explanation)
        $questions = $query->orderByDesc('created_at')
                           ->paginate(20)
                           ->through(fn ($q) => $q->makeVisible(['correct_index', 'explanation', 'explanation_np'])->toArray());

        return response()->json(['success' => true, 'data' => $questions]);
    }

    public function storeQuestion(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category'       => 'required|string|max:100',
            'difficulty'     => 'required|in:easy,medium,hard',
            'question'       => 'required|string',
            'question_np'    => 'nullable|string',
            'options'        => 'required|array|min:2',
            'options.*'      => 'required|string',
            'options_np'     => 'nullable|array',
            'options_np.*'   => 'nullable|string',
            'correct_index'  => 'required|integer|min:0',
            'explanation'    => 'nullable|string',
            'explanation_np' => 'nullable|string',
            'image_url'      => 'nullable|url|max:500',
            'is_active'      => 'boolean',
        ]);

        $question = QuizQuestion::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Question created successfully.',
            'data'    => $question->makeVisible(['correct_index', 'explanation', 'explanation_np']),
        ], 201);
    }

    public function updateQuestion(Request $request, int $id): JsonResponse
    {
        $question = QuizQuestion::findOrFail($id);

        $data = $request->validate([
            'category'       => 'sometimes|string|max:100',
            'difficulty'     => 'sometimes|in:easy,medium,hard',
            'question'       => 'sometimes|string',
            'question_np'    => 'nullable|string',
            'options'        => 'sometimes|array|min:2',
            'options.*'      => 'sometimes|string',
            'options_np'     => 'nullable|array',
            'options_np.*'   => 'nullable|string',
            'correct_index'  => 'sometimes|integer|min:0',
            'explanation'    => 'nullable|string',
            'explanation_np' => 'nullable|string',
            'image_url'      => 'nullable|url|max:500',
            'is_active'      => 'boolean',
        ]);

        $question->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Question updated successfully.',
            'data'    => $question->fresh()->makeVisible(['correct_index', 'explanation', 'explanation_np']),
        ]);
    }

    public function destroyQuestion(int $id): JsonResponse
    {
        QuizQuestion::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Question deleted successfully.',
        ]);
    }

    // =========================================================================
    // Road Signs
    // =========================================================================

    public function listRoadSigns(Request $request): JsonResponse
    {
        $query = RoadSign::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('meaning', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $signs = $query->orderBy('name')->paginate(20);

        return response()->json(['success' => true, 'data' => $signs]);
    }

    public function storeRoadSign(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'name_np'    => 'nullable|string|max:255',
            'meaning'    => 'required|string',
            'meaning_np' => 'nullable|string',
            'category'   => 'required|string|max:100',
            'image_url'  => 'nullable|url|max:500',
            'color_code' => 'nullable|string|max:20',
            'is_active'  => 'boolean',
        ]);

        $sign = RoadSign::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Road sign created successfully.',
            'data'    => $sign,
        ], 201);
    }

    public function updateRoadSign(Request $request, int $id): JsonResponse
    {
        $sign = RoadSign::findOrFail($id);

        $data = $request->validate([
            'name'       => 'sometimes|string|max:255',
            'name_np'    => 'nullable|string|max:255',
            'meaning'    => 'sometimes|string',
            'meaning_np' => 'nullable|string',
            'category'   => 'sometimes|string|max:100',
            'image_url'  => 'nullable|url|max:500',
            'color_code' => 'nullable|string|max:20',
            'is_active'  => 'boolean',
        ]);

        $sign->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Road sign updated successfully.',
            'data'    => $sign->fresh(),
        ]);
    }

    public function destroyRoadSign(int $id): JsonResponse
    {
        RoadSign::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Road sign deleted successfully.',
        ]);
    }

    // =========================================================================
    // Emergency Contacts
    // =========================================================================

    public function listEmergency(Request $request): JsonResponse
    {
        $query = EmergencyContact::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('number', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $contacts = $query->orderBy('sort_order')->paginate(20);

        return response()->json(['success' => true, 'data' => $contacts]);
    }

    public function storeEmergency(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'name_np'     => 'nullable|string|max:255',
            'number'      => 'required|string|max:20',
            'description' => 'nullable|string',
            'category'    => 'required|string|max:100',
            'icon'        => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:20',
            'is_active'   => 'boolean',
            'sort_order'  => 'nullable|integer',
        ]);

        $contact = EmergencyContact::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Emergency contact created successfully.',
            'data'    => $contact,
        ], 201);
    }

    public function updateEmergency(Request $request, int $id): JsonResponse
    {
        $contact = EmergencyContact::findOrFail($id);

        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'name_np'     => 'nullable|string|max:255',
            'number'      => 'sometimes|string|max:20',
            'description' => 'nullable|string',
            'category'    => 'sometimes|string|max:100',
            'icon'        => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:20',
            'is_active'   => 'boolean',
            'sort_order'  => 'nullable|integer',
        ]);

        $contact->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Emergency contact updated successfully.',
            'data'    => $contact->fresh(),
        ]);
    }

    public function destroyEmergency(int $id): JsonResponse
    {
        EmergencyContact::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Emergency contact deleted successfully.',
        ]);
    }

    // =========================================================================
    // Offices
    // =========================================================================

    public function listOffices(Request $request): JsonResponse
    {
        $query = Office::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%")
                  ->orWhere('province', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        if ($district = $request->input('district')) {
            $query->where('district', $district);
        }

        $offices = $query->orderBy('name')->paginate(20);

        return response()->json(['success' => true, 'data' => $offices]);
    }

    public function storeOffice(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'name_np'      => 'nullable|string|max:255',
            'category'     => 'required|string|max:100',
            'address'      => 'nullable|string|max:500',
            'district'     => 'nullable|string|max:100',
            'province'     => 'nullable|string|max:100',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
            'phone'        => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'website'      => 'nullable|url|max:500',
            'office_hours' => 'nullable|string|max:255',
            'is_active'    => 'boolean',
        ]);

        $office = Office::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Office created successfully.',
            'data'    => $office,
        ], 201);
    }

    public function updateOffice(Request $request, int $id): JsonResponse
    {
        $office = Office::findOrFail($id);

        $data = $request->validate([
            'name'         => 'sometimes|string|max:255',
            'name_np'      => 'nullable|string|max:255',
            'category'     => 'sometimes|string|max:100',
            'address'      => 'nullable|string|max:500',
            'district'     => 'nullable|string|max:100',
            'province'     => 'nullable|string|max:100',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
            'phone'        => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'website'      => 'nullable|url|max:500',
            'office_hours' => 'nullable|string|max:255',
            'is_active'    => 'boolean',
        ]);

        $office->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Office updated successfully.',
            'data'    => $office->fresh(),
        ]);
    }

    public function destroyOffice(int $id): JsonResponse
    {
        Office::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Office deleted successfully.',
        ]);
    }
}
