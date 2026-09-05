<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParentStudentLink;
use App\Models\TestAttempt;
use App\Models\UserStreak;
use Illuminate\Http\Request;

class AdminParentalController extends Controller
{
    /** List all parent-student links */
    public function index(Request $request)
    {
        $query = ParentStudentLink::with([
            'parent:id,name,email,phone',
            'student:id,name,email,phone',
        ]);

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $query->whereHas('student', fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
            )->orWhereHas('parent', fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
            );
        }

        $links      = $query->latest()->paginate(25)->withQueryString();
        $totalLinks = ParentStudentLink::count();
        $active     = ParentStudentLink::where('status', 'active')->count();
        $pending    = ParentStudentLink::where('status', 'pending')->count();

        return view('admin.learning.parental.index', compact('links', 'totalLinks', 'active', 'pending'));
    }

    /** View a student's progress as seen by parent */
    public function studentProgress(Request $request, $studentId)
    {
        $link = ParentStudentLink::where('student_id', $studentId)->firstOrFail();
        $student = $link->student;

        $recentAttempts = TestAttempt::where('user_id', $studentId)
            ->with('mockTest:id,title,question_count,duration_minutes')
            ->latest()
            ->limit(20)
            ->get();

        $streak = UserStreak::where('user_id', $studentId)->first();

        $avgScore   = $recentAttempts->avg('score_percentage') ?? 0;
        $totalTests = $recentAttempts->count();
        $passed     = $recentAttempts->where('passed', true)->count();

        return view('admin.learning.parental.student', compact(
            'link', 'student', 'recentAttempts', 'streak', 'avgScore', 'totalTests', 'passed'
        ));
    }

    /** Approve a pending link */
    public function approve(ParentStudentLink $link)
    {
        $link->update(['status' => 'active']);
        return back()->with('success', 'Parent-student link approved.');
    }

    /** Revoke / delete a link */
    public function destroy(ParentStudentLink $link)
    {
        $link->delete();
        return back()->with('success', 'Parent-student link removed.');
    }
}
