<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LiveSession;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    /** GET /programs — listing page */
    public function index(Request $request)
    {
        $query = Program::published()->with('category:id,name_en,icon,color_code');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title_en', 'like', '%' . $request->search . '%')
                  ->orWhere('title_np', 'like', '%' . $request->search . '%')
                  ->orWhere('description_en', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->category) {
            $query->where('learning_category_id', $request->category);
        }

        if ($request->free === '1') {
            $query->where('is_free', true);
        }

        $programs = $query->orderBy('display_order')->paginate(12)->withQueryString();

        $categories = \App\Models\LearningCategory::orderBy('name_en')->get();

        // Today's live sessions across all programs
        $todaysSessions = LiveSession::where('status', 'live')
            ->orWhere(function ($q) {
                $q->where('status', 'scheduled')
                  ->whereDate('starts_at', today());
            })
            ->orderBy('starts_at')
            ->with('course.program:id,title_en,slug')
            ->limit(10)
            ->get();

        return view('programs.index', compact('programs', 'categories', 'todaysSessions'));
    }

    /** GET /programs/{slug} — detail page (Ambition Guru course page) */
    public function show(string $slug)
    {
        $program = Program::published()
            ->where('slug', $slug)
            ->with([
                'category:id,name_en,icon,color_code',
                'courses' => function ($q) {
                    $q->where('is_published', true)
                      ->orderBy('display_order')
                      ->with([
                          'subjects' => function ($sq) {
                              $sq->where('is_active', true)
                                 ->orderBy('display_order')
                                 ->with([
                                     'publishedChapters' => function ($cq) {
                                         $cq->select('id', 'subject_id', 'title_en', 'title_np',
                                                     'content_type', 'duration_minutes',
                                                     'read_time_minutes', 'display_order')
                                            ->orderBy('display_order');
                                     },
                                 ]);
                          },
                      ])
                      ->withCount(['subjects as subject_count']);
                },
            ])
            ->firstOrFail();

        // Today's live sessions for this program
        $todaysSessions = LiveSession::whereHas('course', fn($q) =>
                $q->where('program_id', $program->id)
            )
            ->where(function ($q) {
                $q->where('status', 'live')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'scheduled')
                         ->whereDate('starts_at', today());
                  });
            })
            ->orderBy('starts_at')
            ->get();

        // Use DB-stored aggregate stats (updated by AdminLearningChapterController on every save)
        // Fall back to live computation only if all zeros (fresh program)
        $needsSync = $program->total_lectures === 0
                  && $program->total_videos === 0
                  && $program->total_notes === 0;

        if ($needsSync) {
            $program->syncStats();
            $program->refresh();
        }

        // Count subjects across all courses (loaded above)
        $totalSubjects = $program->courses->sum(fn($c) => $c->subjects->count());

        // Similar programs (same category, exclude current)
        $similarPrograms = Program::published()
            ->where('learning_category_id', $program->learning_category_id)
            ->where('id', '!=', $program->id)
            ->orderBy('display_order')
            ->limit(4)
            ->get();

        return view('programs.show', compact(
            'program',
            'todaysSessions',
            'similarPrograms',
            'totalSubjects'
        ));
    }
}
