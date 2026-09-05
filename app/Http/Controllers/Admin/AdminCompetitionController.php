<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionAttempt;
use App\Models\CompetitionRegistration;
use App\Models\LeaderboardEntry;
use App\Models\LearningCategory;
use App\Models\MockTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;

class AdminCompetitionController extends Controller
{
    public function index(Request $request)
    {
        $query = Competition::with('category:id,name_en', 'mockTest:id,title');

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->category_id) {
            $query->where('learning_category_id', $request->category_id);
        }

        $competitions = $query->latest()->paginate(15)->withQueryString();
        $categories   = LearningCategory::active()->ordered()->get();

        return view('admin.learning.competitions.index', compact('competitions', 'categories'));
    }

    public function create()
    {
        $categories = LearningCategory::active()->ordered()->get();
        $mockTests  = MockTest::active()->latest()->get(['id', 'title', 'category', 'duration_minutes', 'question_count']);

        return view('admin.learning.competitions.create', compact('categories', 'mockTests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'learning_category_id'   => 'required|exists:learning_categories,id',
            'mock_test_id'           => 'required|exists:mock_tests,id',
            'title'                  => 'required|string|max:255',
            'title_np'               => 'nullable|string|max:255',
            'description'            => 'nullable|string',
            'description_np'         => 'nullable|string',
            'registration_open_at'   => 'nullable|date',
            'registration_close_at'  => 'nullable|date|after_or_equal:registration_open_at',
            'starts_at'              => 'required|date',
            'ends_at'                => 'required|date|after:starts_at',
            'max_participants'       => 'nullable|integer|min:2',
            'entry_fee'              => 'nullable|numeric|min:0',
            'prize_pool'             => 'nullable|numeric|min:0',
            'prize_distribution'     => 'nullable|json',
            'banner_file'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $bannerUrl = null;
        if ($request->hasFile('banner_file')) {
            $path      = $request->file('banner_file')->store('learning/competitions', 'public');
            $bannerUrl = Storage::url($path);
        }

        $isFree           = ((float)($request->entry_fee ?? 0)) == 0;
        $prizeDistribution= $request->prize_distribution
            ? json_decode($request->prize_distribution, true)
            : null;

        Competition::create([
            'learning_category_id'  => $request->learning_category_id,
            'mock_test_id'          => $request->mock_test_id,
            'title'                 => $request->title,
            'title_np'              => $request->title_np,
            'description'           => $request->description,
            'description_np'        => $request->description_np,
            'banner_url'            => $bannerUrl,
            'registration_open_at'  => $request->registration_open_at,
            'registration_close_at' => $request->registration_close_at,
            'starts_at'             => $request->starts_at,
            'ends_at'               => $request->ends_at,
            'max_participants'      => $request->max_participants,
            'entry_fee'             => $request->entry_fee ?? 0,
            'is_free'               => $isFree,
            'prize_pool'            => $request->prize_pool ?? 0,
            'prize_distribution'    => $prizeDistribution,
            'status'                => Competition::STATUS_DRAFT,
            'created_by'            => auth()->id(),
        ]);

        return redirect()->route('admin.learning.competitions.index')
            ->with('success', 'Competition created successfully.');
    }

    public function show(Competition $competition)
    {
        $competition->load(['category:id,name_en', 'mockTest:id,title,question_count,duration_minutes']);

        $registrationCount = $competition->registrations()->where('payment_status', 'paid')->count();
        $attemptCount      = $competition->attempts()->count();
        $topAttempts       = $competition->attempts()
            ->with('user:id,name,email,avatar')
            ->orderByDesc('score_percentage')
            ->orderBy('time_taken_seconds')
            ->limit(10)
            ->get();

        return view('admin.learning.competitions.show', compact(
            'competition', 'registrationCount', 'attemptCount', 'topAttempts'
        ));
    }

    public function edit(Competition $competition)
    {
        $categories = LearningCategory::active()->ordered()->get();
        $mockTests  = MockTest::active()->latest()->get(['id', 'title', 'category', 'duration_minutes', 'question_count']);

        return view('admin.learning.competitions.edit', compact('competition', 'categories', 'mockTests'));
    }

    public function update(Request $request, Competition $competition)
    {
        $request->validate([
            'learning_category_id'   => 'required|exists:learning_categories,id',
            'mock_test_id'           => 'required|exists:mock_tests,id',
            'title'                  => 'required|string|max:255',
            'title_np'               => 'nullable|string|max:255',
            'description'            => 'nullable|string',
            'description_np'         => 'nullable|string',
            'registration_open_at'   => 'nullable|date',
            'registration_close_at'  => 'nullable|date',
            'starts_at'              => 'required|date',
            'ends_at'                => 'required|date|after:starts_at',
            'max_participants'       => 'nullable|integer|min:2',
            'entry_fee'              => 'nullable|numeric|min:0',
            'prize_pool'             => 'nullable|numeric|min:0',
            'prize_distribution'     => 'nullable|json',
            'status'                 => 'required|in:draft,open,ongoing,completed,cancelled',
            'banner_file'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $bannerUrl = $competition->banner_url;
        if ($request->hasFile('banner_file')) {
            $path      = $request->file('banner_file')->store('learning/competitions', 'public');
            $bannerUrl = Storage::url($path);
        }

        $isFree            = ((float)($request->entry_fee ?? 0)) == 0;
        $prizeDistribution = $request->prize_distribution
            ? json_decode($request->prize_distribution, true)
            : $competition->prize_distribution;

        $competition->update([
            'learning_category_id'  => $request->learning_category_id,
            'mock_test_id'          => $request->mock_test_id,
            'title'                 => $request->title,
            'title_np'              => $request->title_np,
            'description'           => $request->description,
            'description_np'        => $request->description_np,
            'banner_url'            => $bannerUrl,
            'registration_open_at'  => $request->registration_open_at,
            'registration_close_at' => $request->registration_close_at,
            'starts_at'             => $request->starts_at,
            'ends_at'               => $request->ends_at,
            'max_participants'      => $request->max_participants,
            'entry_fee'             => $request->entry_fee ?? 0,
            'is_free'               => $isFree,
            'prize_pool'            => $request->prize_pool ?? 0,
            'prize_distribution'    => $prizeDistribution,
            'status'                => $request->status,
        ]);

        return redirect()->route('admin.learning.competitions.index')
            ->with('success', 'Competition updated successfully.');
    }

    public function destroy(Competition $competition)
    {
        $competition->delete();
        return back()->with('success', 'Competition deleted.');
    }

    /** View all registrations for a competition */
    public function registrations(Competition $competition)
    {
        $registrations = CompetitionRegistration::where('competition_id', $competition->id)
            ->with('user:id,name,email,phone,avatar')
            ->latest()
            ->paginate(30);

        return view('admin.learning.competitions.registrations', compact('competition', 'registrations'));
    }

    /** View leaderboard for a competition */
    public function leaderboard(Competition $competition)
    {
        $entries = LeaderboardEntry::where('competition_id', $competition->id)
            ->with('user:id,name,email,avatar')
            ->orderBy('rank')
            ->paginate(50);

        return view('admin.learning.competitions.leaderboard', compact('competition', 'entries'));
    }

    /** Compute leaderboard and assign ranks + prizes */
    public function computeLeaderboard(Competition $competition)
    {
        if (!in_array($competition->status, [Competition::STATUS_ONGOING, Competition::STATUS_COMPLETED])) {
            return back()->with('error', 'Competition must be ongoing or completed to compute leaderboard.');
        }

        DB::transaction(function () use ($competition) {
            // Delete existing leaderboard entries
            LeaderboardEntry::where('competition_id', $competition->id)->delete();

            // Rank all attempts: highest score first, then fastest time
            $attempts = CompetitionAttempt::where('competition_id', $competition->id)
                ->orderByDesc('score_percentage')
                ->orderBy('time_taken_seconds')
                ->orderBy('created_at') // earliest submission wins ties
                ->get();

            foreach ($attempts as $rank => $attempt) {
                $prize = $competition->getPrizeForRank($rank + 1);

                LeaderboardEntry::create([
                    'competition_id'     => $competition->id,
                    'user_id'            => $attempt->user_id,
                    'score_percentage'   => $attempt->score_percentage,
                    'correct_answers'    => $attempt->correct_answers,
                    'time_taken_seconds' => $attempt->time_taken_seconds,
                    'rank'               => $rank + 1,
                    'prize_won'          => $prize,
                    'computed_at'        => now(),
                ]);

                // Update attempt rank too
                $attempt->update(['rank' => $rank + 1, 'prize_won' => $prize]);
            }
        });

        return back()->with('success', 'Leaderboard computed successfully. ' .
            LeaderboardEntry::where('competition_id', $competition->id)->count() . ' entries ranked.');
    }

    /** Announce winners — mark competition as completed with winners_announced_at */
    public function announceWinners(Competition $competition)
    {
        if (LeaderboardEntry::where('competition_id', $competition->id)->doesntExist()) {
            return back()->with('error', 'Please compute the leaderboard first.');
        }

        $competition->update([
            'status'               => Competition::STATUS_COMPLETED,
            'winners_announced_at' => now(),
        ]);

        // Fire the notify command inline (or dispatch a job)
        \Artisan::call('learning:notify-winners', ['competition' => $competition->id]);

        return back()->with('success', 'Winners announced and notifications sent.');
    }

    /** Export competition results as CSV */
    public function exportResults(Competition $competition)
    {
        $entries = LeaderboardEntry::where('competition_id', $competition->id)
            ->with('user:id,name,email,phone')
            ->orderBy('rank')
            ->get();

        $filename = 'competition-' . $competition->id . '-results.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($entries) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Rank', 'Name', 'Email', 'Phone', 'Score %', 'Correct Answers', 'Time (sec)', 'Prize Won']);
            foreach ($entries as $e) {
                fputcsv($handle, [
                    $e->rank,
                    $e->user?->name,
                    $e->user?->email,
                    $e->user?->phone,
                    $e->score_percentage,
                    $e->correct_answers,
                    $e->time_taken_seconds,
                    $e->prize_won,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
