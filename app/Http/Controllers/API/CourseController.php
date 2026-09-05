<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Doubt;
use App\Models\DoubtAnswer;
use App\Models\LearningCategory;
use App\Models\LearningChapter;
use App\Models\LiveSession;
use App\Models\Program;
use App\Models\Subject;
use App\Models\UserLearningProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    // =========================================================================
    // PROGRAMS
    // =========================================================================

    /** GET /api/v1/programs  — list all published programs */
    public function programs(Request $request): JsonResponse
    {
        $query = Program::published()->with('category:id,slug,name_en,icon,color_code');

        if ($request->category_slug) {
            $cat = LearningCategory::where('slug', $request->category_slug)->first();
            if ($cat) $query->where('learning_category_id', $cat->id);
        }

        $programs = $query->orderBy('display_order')->get()
            ->map(fn($p) => [
                'id'             => $p->id,
                'slug'           => $p->slug,
                'title_en'       => $p->title_en,
                'title_np'       => $p->title_np,
                'description_en' => $p->description_en,
                'description_np' => $p->description_np,
                'thumbnail_url'  => $p->thumbnail_url,
                'icon'           => $p->icon,
                'color_code'     => $p->color_code,
                'is_free'        => $p->is_free,
                'price'          => $p->price,
                'enrolled_count' => $p->enrolled_count,
                'total_chapters' => $p->total_chapters,
                'category'       => $p->category ? ['slug' => $p->category->slug, 'name_en' => $p->category->name_en, 'icon' => $p->category->icon] : null,
                'course_count'   => $p->courses()->count(),
            ]);

        return response()->json(['success' => true, 'data' => $programs]);
    }

    /** GET /api/v1/programs/{slug} — program with all courses + subjects */
    public function program(Request $request, string $slug): JsonResponse
    {
        $program = Program::published()->where('slug', $slug)
            ->with(['category:id,slug,name_en,icon', 'courses' => function ($q) {
                $q->published()->with(['subjects' => fn($s) => $s->active()->orderBy('display_order')]);
            }])
            ->firstOrFail();

        $userId = $request->user()?->id;
        $enrolledCourseIds = $userId
            ? CourseEnrollment::where('user_id', $userId)
                ->whereIn('course_id', $program->courses->pluck('id'))
                ->pluck('course_id')->toArray()
            : [];

        $courses = $program->courses->map(fn($c) => [
            'id'           => $c->id,
            'title_en'     => $c->title_en,
            'title_np'     => $c->title_np,
            'description_en'=> $c->description_en,
            'thumbnail_url'=> $c->thumbnail_url,
            'total_subjects'=> $c->total_subjects,
            'total_chapters'=> $c->total_chapters,
            'is_enrolled'  => in_array($c->id, $enrolledCourseIds),
            'subjects'     => $c->subjects->map(fn($s) => [
                'id'           => $s->id,
                'title_en'     => $s->title_en,
                'title_np'     => $s->title_np,
                'icon'         => $s->icon,
                'color_code'   => $s->color_code,
                'chapter_count'=> $s->chapter_count,
            ]),
        ]);

        return response()->json(['success' => true, 'data' => [
            'program' => [
                'id'             => $program->id,
                'slug'           => $program->slug,
                'title_en'       => $program->title_en,
                'title_np'       => $program->title_np,
                'description_en' => $program->description_en,
                'banner_url'     => $program->banner_url,
                'is_free'        => $program->is_free,
                'category'       => $program->category,
            ],
            'courses' => $courses,
        ]]);
    }

    // =========================================================================
    // COURSES
    // =========================================================================

    /** GET /api/v1/courses/{id} — course detail with full subject+chapter tree */
    public function course(Request $request, int $id): JsonResponse
    {
        $course = Course::published()
            ->with(['subjects' => fn($q) => $q->active()->with('publishedChapters')])
            ->findOrFail($id);

        $userId = $request->user()?->id;
        $enrollment = $userId
            ? CourseEnrollment::where('user_id', $userId)->where('course_id', $id)->first()
            : null;

        $readIds = $userId
            ? UserLearningProgress::where('user_id', $userId)->pluck('learning_chapter_id')->toArray()
            : [];

        $subjects = $course->subjects->map(fn($s) => [
            'id'           => $s->id,
            'title_en'     => $s->title_en,
            'title_np'     => $s->title_np,
            'icon'         => $s->icon,
            'color_code'   => $s->color_code,
            'chapter_count'=> $s->chapter_count,
            'chapters'     => $s->publishedChapters->map(fn($ch) => [
                'id'               => $ch->id,
                'title_en'         => $ch->title_en,
                'title_np'         => $ch->title_np,
                'summary_en'       => $ch->summary_en,
                'image_url'        => $ch->image_url,
                'video_url'        => $ch->video_url,
                'read_time_minutes'=> $ch->read_time_minutes,
                'display_order'    => $ch->display_order,
                'is_read'          => in_array($ch->id, $readIds),
            ]),
        ]);

        return response()->json(['success' => true, 'data' => [
            'course'     => [
                'id'             => $course->id,
                'title_en'       => $course->title_en,
                'title_np'       => $course->title_np,
                'description_en' => $course->description_en,
                'total_subjects' => $course->total_subjects,
                'total_chapters' => $course->total_chapters,
            ],
            'is_enrolled'   => (bool) $enrollment,
            'progress_pct'  => $enrollment?->progress_pct ?? 0,
            'subjects'      => $subjects,
        ]]);
    }

    /** POST /api/v1/courses/{id}/enroll  [auth] */
    public function enroll(Request $request, int $id): JsonResponse
    {
        $course = Course::published()->findOrFail($id);

        if (CourseEnrollment::where('user_id', $request->user()->id)->where('course_id', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Already enrolled.'], 422);
        }

        CourseEnrollment::create([
            'user_id'     => $request->user()->id,
            'course_id'   => $id,
            'enrolled_at' => now(),
        ]);

        $course->program->increment('enrolled_count');

        return response()->json(['success' => true, 'message' => "Enrolled in {$course->title_en} successfully!"], 201);
    }

    /** GET /api/v1/courses/my  [auth] — enrolled courses with progress */
    public function myCourses(Request $request): JsonResponse
    {
        $enrollments = CourseEnrollment::where('user_id', $request->user()->id)
            ->with(['course.program:id,title_en,icon,color_code'])
            ->latest('enrolled_at')
            ->get()
            ->map(fn($e) => [
                'course_id'      => $e->course_id,
                'course_title'   => $e->course?->title_en,
                'program_title'  => $e->course?->program?->title_en,
                'program_icon'   => $e->course?->program?->icon,
                'progress_pct'   => $e->progress_pct,
                'enrolled_at'    => $e->enrolled_at?->toDateString(),
                'completed_at'   => $e->completed_at?->toDateString(),
            ]);

        return response()->json(['success' => true, 'data' => $enrollments]);
    }

    // =========================================================================
    // LIVE SESSIONS
    // =========================================================================

    /** GET /api/v1/live-sessions — upcoming + live sessions (public) */
    public function liveSessions(Request $request): JsonResponse
    {
        $query = LiveSession::with('category:id,slug,name_en,icon');

        if ($request->category_slug) {
            $cat = LearningCategory::where('slug', $request->category_slug)->first();
            if ($cat) $query->where('learning_category_id', $cat->id);
        }

        if ($request->type === 'recorded') {
            $sessions = $query->recorded()->orderByDesc('starts_at')->paginate(10);
        } elseif ($request->type === 'live') {
            $sessions = $query->live()->orderBy('starts_at')->paginate(10);
        } else {
            // Default: upcoming + currently live
            $sessions = $query->whereIn('status', ['scheduled', 'live'])
                ->orderBy('starts_at')->paginate(10);
        }

        return response()->json(['success' => true, 'data' => $sessions]);
    }

    /** GET /api/v1/live-sessions/{id} */
    public function liveSession(int $id): JsonResponse
    {
        $session = LiveSession::with('category:id,slug,name_en,icon')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $session]);
    }

    // =========================================================================
    // DOUBTS / Q&A
    // =========================================================================

    /** GET /api/v1/doubts — list doubts (public) */
    public function doubts(Request $request): JsonResponse
    {
        $query = Doubt::with(['user:id,name,avatar', 'category:id,slug,name_en'])
            ->withCount('answers');

        if ($request->category_slug) {
            $cat = LearningCategory::where('slug', $request->category_slug)->first();
            if ($cat) $query->where('learning_category_id', $cat->id);
        }
        if ($request->status) $query->where('status', $request->status);
        if ($request->chapter_id) $query->where('learning_chapter_id', $request->chapter_id);

        $doubts = $query->orderByDesc('is_pinned')->latest()->paginate(15);

        return response()->json(['success' => true, 'data' => $doubts]);
    }

    /** GET /api/v1/doubts/{id} — doubt with all answers */
    public function doubt(int $id): JsonResponse
    {
        $doubt = Doubt::with([
            'user:id,name,avatar',
            'category:id,slug,name_en',
            'answers.user:id,name,avatar',
        ])->findOrFail($id);

        return response()->json(['success' => true, 'data' => $doubt]);
    }

    /** POST /api/v1/doubts  [auth] — ask a new doubt */
    public function storeDoubt(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'                  => 'required|string|max:255',
            'body'                   => 'required|string|max:2000',
            'learning_category_id'   => 'nullable|exists:learning_categories,id',
            'learning_chapter_id'    => 'nullable|exists:learning_chapters,id',
        ]);

        $doubt = Doubt::create(array_merge($data, ['user_id' => $request->user()->id]));

        return response()->json(['success' => true, 'data' => $doubt->load('user:id,name,avatar')], 201);
    }

    /** POST /api/v1/doubts/{id}/answers  [auth] — answer a doubt */
    public function answerDoubt(Request $request, int $id): JsonResponse
    {
        $doubt = Doubt::findOrFail($id);
        $data  = $request->validate(['body' => 'required|string|max:3000']);

        $answer = DoubtAnswer::create([
            'doubt_id'      => $doubt->id,
            'user_id'       => $request->user()->id,
            'body'          => $data['body'],
            'is_instructor' => $request->user()->isAdmin(),
        ]);

        $doubt->increment('answers_count');
        if ($doubt->status === Doubt::STATUS_OPEN) {
            $doubt->update(['status' => Doubt::STATUS_ANSWERED]);
        }

        return response()->json(['success' => true, 'data' => $answer->load('user:id,name,avatar')], 201);
    }

    /** POST /api/v1/doubts/{id}/answers/{answerId}/accept  [auth] */
    public function acceptAnswer(Request $request, int $doubtId, int $answerId): JsonResponse
    {
        $doubt  = Doubt::where('user_id', $request->user()->id)->findOrFail($doubtId);
        $answer = DoubtAnswer::where('doubt_id', $doubtId)->findOrFail($answerId);

        // Unaccept previous
        DoubtAnswer::where('doubt_id', $doubtId)->update(['is_accepted' => false]);
        $answer->update(['is_accepted' => true]);
        $doubt->update(['status' => Doubt::STATUS_CLOSED]);

        return response()->json(['success' => true, 'message' => 'Answer accepted.']);
    }

    /** GET /api/v1/doubts/my  [auth] */
    public function myDoubts(Request $request): JsonResponse
    {
        $doubts = Doubt::where('user_id', $request->user()->id)
            ->with('category:id,slug,name_en')
            ->withCount('answers')
            ->latest()->paginate(15);

        return response()->json(['success' => true, 'data' => $doubts]);
    }
}
