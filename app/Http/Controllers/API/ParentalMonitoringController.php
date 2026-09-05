<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ParentStudentLink;
use App\Models\TestAttempt;
use App\Models\User;
use App\Models\UserStreak;
use App\Models\UserLearningProgress;
use App\Models\DailyQuizEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ParentalMonitoringController extends Controller
{
    // ── Helper: assert approved link between caller and student ───────────

    private function assertApprovedLink(int $parentId, int $studentId): ?ParentStudentLink
    {
        return ParentStudentLink::where('parent_id', $parentId)
            ->where('student_id', $studentId)
            ->where('status', 'approved')
            ->first();
    }

    // =========================================================================
    // PARENT-SIDE: manage children
    // =========================================================================

    /**
     * GET /api/v1/parental/my-children
     * List all students linked to the authenticated parent.
     */
    public function myChildren(Request $request): JsonResponse
    {
        $links = ParentStudentLink::where('parent_id', $request->user()->id)
            ->with('student:id,name,profile_photo_url')
            ->get()
            ->map(fn ($l) => [
                'link_id'   => $l->id,
                'status'    => $l->status,
                'student'   => $l->student ? [
                    'id'    => $l->student->id,
                    'name'  => $l->student->name,
                    'photo' => $l->student->profile_photo_url ?? null,
                ] : null,
                'linked_at' => $l->updated_at?->toDateTimeString(),
            ]);

        return response()->json(['success' => true, 'data' => $links]);
    }

    /**
     * GET /api/v1/parental/child/{id}/progress
     * Full progress dashboard for a linked student.
     */
    public function childProgress(Request $request, int $id): JsonResponse
    {
        if (!$this->assertApprovedLink($request->user()->id, $id)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have an approved link with this student.',
            ], 403);
        }

        $streak      = UserStreak::forUser($id);
        $recentTests = TestAttempt::where('user_id', $id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['id', 'category', 'score', 'total_questions', 'percentile_score', 'created_at']);

        $totalChapters = \App\Models\LearningChapter::published()->count();
        $readChapters  = UserLearningProgress::where('user_id', $id)->count();

        $quizStats = DailyQuizEntry::where('user_id', $id)
            ->selectRaw('COUNT(*) as total, SUM(is_correct) as correct')
            ->first();

        return response()->json([
            'success' => true,
            'data'    => [
                'student_id'               => $id,
                'current_streak'           => $streak->current_streak,
                'longest_streak'           => $streak->longest_streak,
                'total_study_days'         => $streak->total_study_days,
                'daily_quiz_streak'        => $streak->daily_quiz_streak,
                'syllabus_completion_pct'  => $totalChapters > 0
                    ? round(($readChapters / $totalChapters) * 100) : 0,
                'chapters_read'            => $readChapters,
                'daily_quiz_total'         => (int) ($quizStats->total ?? 0),
                'daily_quiz_correct'       => (int) ($quizStats->correct ?? 0),
                'recent_tests'             => $recentTests,
            ],
        ]);
    }

    /**
     * POST /api/v1/parental/link
     * Parent requests to link with a student (by student_id).
     */
    public function requestLink(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|integer|exists:users,id',
        ]);

        if ($request->user()->id == $request->student_id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot link to yourself.',
            ], 422);
        }

        $link = ParentStudentLink::updateOrCreate(
            [
                'parent_id'  => $request->user()->id,
                'student_id' => $request->student_id,
            ],
            ['status' => 'pending']
        );

        return response()->json([
            'success' => true,
            'message' => 'Link request sent. The student must accept it.',
            'data'    => [
                'link_id' => $link->id,
                'status'  => $link->status,
            ],
        ]);
    }

    /**
     * DELETE /api/v1/parental/link/{id}
     * Parent removes an existing link.
     */
    public function removeLink(Request $request, int $id): JsonResponse
    {
        $link = ParentStudentLink::where('id', $id)
            ->where('parent_id', $request->user()->id)
            ->firstOrFail();

        $link->delete();

        return response()->json(['success' => true, 'message' => 'Link removed.']);
    }

    // =========================================================================
    // STUDENT-SIDE: manage parents
    // =========================================================================

    /**
     * GET /api/v1/parental/my-parents
     * List all parents linked to the authenticated student.
     */
    public function myParents(Request $request): JsonResponse
    {
        $links = ParentStudentLink::where('student_id', $request->user()->id)
            ->with('parent:id,name,profile_photo_url')
            ->get()
            ->map(fn ($l) => [
                'link_id' => $l->id,
                'status'  => $l->status,
                'parent'  => $l->parent ? [
                    'id'    => $l->parent->id,
                    'name'  => $l->parent->name,
                    'photo' => $l->parent->profile_photo_url ?? null,
                ] : null,
                'linked_at' => $l->updated_at?->toDateTimeString(),
            ]);

        return response()->json(['success' => true, 'data' => $links]);
    }

    /**
     * POST /api/v1/parental/link/{id}/accept
     * Student accepts a pending parent link.
     */
    public function acceptLink(Request $request, int $id): JsonResponse
    {
        $link = ParentStudentLink::where('id', $id)
            ->where('student_id', $request->user()->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $link->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => 'Parent link accepted.',
        ]);
    }
}
