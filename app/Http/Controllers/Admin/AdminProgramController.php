<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LearningCategory;
use App\Models\LearningChapter;
use App\Models\LiveSession;
use App\Models\Program;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminProgramController extends Controller
{
    // ── Programs ────────────────────────────────────────────────────────────

    public function index()
    {
        $programs   = Program::with('category:id,name_en,icon')->withCount('courses')->orderBy('display_order')->paginate(20);
        $categories = LearningCategory::active()->ordered()->get();
        return view('admin.learning.programs.index', compact('programs', 'categories'));
    }

    public function create()
    {
        $categories = LearningCategory::active()->ordered()->get();
        return view('admin.learning.programs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'learning_category_id' => 'required|exists:learning_categories,id',
            'title_en'             => 'required|string|max:200',
            'title_np'             => 'nullable|string|max:200',
            'description_en'       => 'nullable|string',
            'description_np'       => 'nullable|string',
            'icon'                 => 'nullable|string|max:10',
            'color_code'           => 'nullable|string|max:10',
            'price'                => 'nullable|numeric|min:0',
            'display_order'        => 'nullable|integer|min:0',
            'thumbnail_file'       => 'nullable|image|max:5120',
        ]);

        $slug = $this->uniqueSlug($request->title_en);
        $thumb = $this->upload($request, 'thumbnail_file', 'learning/programs');

        Program::create([
            'learning_category_id' => $request->learning_category_id,
            'slug'            => $slug,
            'title_en'        => $request->title_en,
            'title_np'        => $request->title_np,
            'description_en'  => $request->description_en,
            'description_np'  => $request->description_np,
            'icon'            => $request->icon,
            'color_code'      => $request->color_code,
            'price'           => $request->price ?? 0,
            'is_free'         => ($request->price ?? 0) == 0,
            'thumbnail_url'   => $thumb,
            'display_order'   => $request->display_order ?? 0,
            'is_published'    => $request->has('is_published'),
        ]);

        return redirect()->route('admin.learning.programs.index')->with('success', 'Program created.');
    }

    public function show(Program $program)
    {
        $program->load(['category:id,name_en,icon', 'courses.subjects']);
        return view('admin.learning.programs.show', compact('program'));
    }

    public function edit(Program $program)
    {
        $categories = LearningCategory::active()->ordered()->get();
        return view('admin.learning.programs.edit', compact('program', 'categories'));
    }

    public function update(Request $request, Program $program)
    {
        $request->validate([
            'learning_category_id' => 'required|exists:learning_categories,id',
            'title_en'             => 'required|string|max:200',
            'title_np'             => 'nullable|string|max:200',
            'description_en'       => 'nullable|string',
            'icon'                 => 'nullable|string|max:10',
            'color_code'           => 'nullable|string|max:10',
            'price'                => 'nullable|numeric|min:0',
            'display_order'        => 'nullable|integer|min:0',
            'thumbnail_file'       => 'nullable|image|max:5120',
        ]);

        $thumb = $this->upload($request, 'thumbnail_file', 'learning/programs') ?? $program->thumbnail_url;

        $program->update([
            'learning_category_id' => $request->learning_category_id,
            'title_en'        => $request->title_en,
            'title_np'        => $request->title_np,
            'description_en'  => $request->description_en,
            'description_np'  => $request->description_np,
            'icon'            => $request->icon,
            'color_code'      => $request->color_code,
            'price'           => $request->price ?? 0,
            'is_free'         => ($request->price ?? 0) == 0,
            'thumbnail_url'   => $thumb,
            'display_order'   => $request->display_order ?? 0,
            'is_published'    => $request->has('is_published'),
        ]);

        return redirect()->route('admin.learning.programs.index')->with('success', 'Program updated.');
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return back()->with('success', 'Program deleted.');
    }

    // ── Courses ─────────────────────────────────────────────────────────────

    public function createCourse(Program $program)
    {
        return view('admin.learning.programs.course-form', compact('program'));
    }

    public function storeCourse(Request $request, Program $program)
    {
        $request->validate([
            'title_en'       => 'required|string|max:200',
            'title_np'       => 'nullable|string|max:200',
            'description_en' => 'nullable|string',
            'display_order'  => 'nullable|integer|min:0',
            'thumbnail_file' => 'nullable|image|max:5120',
        ]);

        $thumb = $this->upload($request, 'thumbnail_file', 'learning/courses');

        Course::create([
            'program_id'     => $program->id,
            'title_en'       => $request->title_en,
            'title_np'       => $request->title_np,
            'description_en' => $request->description_en,
            'thumbnail_url'  => $thumb,
            'display_order'  => $request->display_order ?? 0,
            'is_published'   => $request->has('is_published'),
        ]);

        return redirect()->route('admin.learning.programs.show', $program)->with('success', 'Course added.');
    }

    public function editCourse(Course $course)
    {
        $program = $course->program;
        return view('admin.learning.programs.course-form', compact('program', 'course'));
    }

    public function updateCourse(Request $request, Course $course)
    {
        $request->validate([
            'title_en'       => 'required|string|max:200',
            'title_np'       => 'nullable|string|max:200',
            'description_en' => 'nullable|string',
            'display_order'  => 'nullable|integer|min:0',
        ]);

        $course->update([
            'title_en'       => $request->title_en,
            'title_np'       => $request->title_np,
            'description_en' => $request->description_en,
            'display_order'  => $request->display_order ?? 0,
            'is_published'   => $request->has('is_published'),
        ]);

        return redirect()->route('admin.learning.programs.show', $course->program)->with('success', 'Course updated.');
    }

    public function destroyCourse(Course $course)
    {
        $program = $course->program;
        $course->delete();
        return redirect()->route('admin.learning.programs.show', $program)->with('success', 'Course deleted.');
    }

    // ── Subjects ────────────────────────────────────────────────────────────

    public function storeSubject(Request $request, Course $course)
    {
        $request->validate([
            'title_en'    => 'required|string|max:200',
            'title_np'    => 'nullable|string|max:200',
            'icon'        => 'nullable|string|max:10',
            'color_code'  => 'nullable|string|max:10',
            'display_order'=> 'nullable|integer|min:0',
        ]);

        Subject::create([
            'course_id'     => $course->id,
            'title_en'      => $request->title_en,
            'title_np'      => $request->title_np,
            'icon'          => $request->icon,
            'color_code'    => $request->color_code,
            'display_order' => $request->display_order ?? 0,
            'is_active'     => $request->has('is_active'),
        ]);

        $course->syncCounts();

        return back()->with('success', 'Subject added.');
    }

    public function destroySubject(Subject $subject)
    {
        $course = $subject->course;
        $subject->delete();
        $course->syncCounts();
        return back()->with('success', 'Subject deleted.');
    }

    // ── Live Sessions ────────────────────────────────────────────────────────

    public function liveSessions()
    {
        $sessions   = LiveSession::with('category:id,name_en,icon')->orderByDesc('starts_at')->paginate(20);
        $categories = LearningCategory::active()->ordered()->get();
        $courses    = Course::published()->get(['id', 'title_en']);
        return view('admin.learning.live-sessions.index', compact('sessions', 'categories', 'courses'));
    }

    public function createLiveSession()
    {
        $categories = LearningCategory::active()->ordered()->get();
        $courses    = Course::published()->get(['id', 'title_en']);
        return view('admin.learning.live-sessions.create', compact('categories', 'courses'));
    }

    public function storeLiveSession(Request $request)
    {
        $request->validate([
            'title_en'             => 'required|string|max:255',
            'title_np'             => 'nullable|string|max:255',
            'description'          => 'nullable|string',
            'type'                 => 'required|in:live,recorded,doubt_clearing',
            'learning_category_id' => 'nullable|exists:learning_categories,id',
            'course_id'            => 'nullable|exists:courses,id',
            'instructor_name'      => 'nullable|string|max:100',
            'stream_url'           => 'nullable|url|max:500',
            'recording_url'        => 'nullable|url|max:500',
            'starts_at'            => 'required|date',
            'duration_minutes'     => 'required|integer|min:15|max:480',
        ]);

        LiveSession::create([
            'learning_category_id' => $request->learning_category_id ?: null,
            'course_id'            => $request->course_id ?: null,
            'title_en'             => $request->title_en,
            'title_np'             => $request->title_np,
            'description'          => $request->description,
            'type'                 => $request->type,
            'instructor_name'      => $request->instructor_name,
            'stream_url'           => $request->stream_url,
            'recording_url'        => $request->recording_url,
            'starts_at'            => $request->starts_at,
            'duration_minutes'     => $request->duration_minutes,
            'status'               => $request->status ?? 'scheduled',
            'is_free'              => $request->has('is_free'),
        ]);

        return redirect()->route('admin.learning.live-sessions.index')->with('success', 'Live session created.');
    }

    public function editLiveSession(LiveSession $liveSession)
    {
        $categories = LearningCategory::active()->ordered()->get();
        $courses    = Course::published()->get(['id', 'title_en']);
        return view('admin.learning.live-sessions.edit', compact('liveSession', 'categories', 'courses'));
    }

    public function updateLiveSession(Request $request, LiveSession $liveSession)
    {
        $request->validate([
            'title_en'        => 'required|string|max:255',
            'type'            => 'required|in:live,recorded,doubt_clearing',
            'starts_at'       => 'required|date',
            'duration_minutes'=> 'required|integer|min:15',
            'status'          => 'required|in:scheduled,live,ended,cancelled',
        ]);

        $liveSession->update($request->only([
            'title_en', 'title_np', 'description', 'type',
            'learning_category_id', 'course_id', 'instructor_name',
            'stream_url', 'recording_url', 'starts_at', 'duration_minutes', 'status',
        ]));
        $liveSession->update(['is_free' => $request->has('is_free')]);

        return redirect()->route('admin.learning.live-sessions.index')->with('success', 'Session updated.');
    }

    public function destroyLiveSession(LiveSession $liveSession)
    {
        $liveSession->delete();
        return back()->with('success', 'Session deleted.');
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    private function uniqueSlug(string $title): string
    {
        $slug = Str::slug($title); $base = $slug; $i = 1;
        while (Program::where('slug', $slug)->exists()) { $slug = $base . '-' . $i++; }
        return $slug;
    }

    private function upload(Request $request, string $field, string $path): ?string
    {
        if ($request->hasFile($field)) {
            return Storage::url($request->file($field)->store($path, 'public'));
        }
        return null;
    }
}
