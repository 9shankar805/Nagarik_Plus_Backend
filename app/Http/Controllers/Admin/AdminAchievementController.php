<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\UserAchievement;
use Illuminate\Http\Request;

class AdminAchievementController extends Controller
{
    public function index()
    {
        $achievements = Achievement::withCount('userAchievements')->latest()->paginate(20);
        return view('admin.learning.achievements.index', compact('achievements'));
    }

    public function create()
    {
        $types = [
            'streak'      => 'Study Streak',
            'daily'       => 'Daily Quiz',
            'quiz'        => 'Quiz Attempts',
            'test'        => 'Tests Passed',
            'chapter'     => 'Chapters Read',
            'competition' => 'Competitions',
            'special'     => 'Special',
        ];
        return view('admin.learning.achievements.create', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug'           => 'required|string|max:80|unique:achievements,slug',
            'title_en'       => 'required|string|max:150',
            'title_np'       => 'nullable|string|max:150',
            'description_en' => 'nullable|string',
            'description_np' => 'nullable|string',
            'icon'           => 'nullable|string|max:10',
            'badge_color'    => 'nullable|string|max:10',
            'type'           => 'required|in:streak,daily,quiz,test,chapter,competition,special',
            'threshold'      => 'required|integer|min:1',
        ]);

        Achievement::create([
            'slug'           => $request->slug,
            'title_en'       => $request->title_en,
            'title_np'       => $request->title_np,
            'description_en' => $request->description_en,
            'description_np' => $request->description_np,
            'icon'           => $request->icon,
            'badge_color'    => $request->badge_color ?? '#4A5D4A',
            'type'           => $request->type,
            'threshold'      => $request->threshold,
            'is_active'      => $request->has('is_active'),
        ]);

        return redirect()->route('admin.learning.achievements.index')
            ->with('success', 'Achievement created.');
    }

    public function edit(Achievement $achievement)
    {
        $types = [
            'streak'      => 'Study Streak',
            'daily'       => 'Daily Quiz',
            'quiz'        => 'Quiz Attempts',
            'test'        => 'Tests Passed',
            'chapter'     => 'Chapters Read',
            'competition' => 'Competitions',
            'special'     => 'Special',
        ];
        return view('admin.learning.achievements.edit', compact('achievement', 'types'));
    }

    public function update(Request $request, Achievement $achievement)
    {
        $request->validate([
            'title_en'       => 'required|string|max:150',
            'title_np'       => 'nullable|string|max:150',
            'description_en' => 'nullable|string',
            'description_np' => 'nullable|string',
            'icon'           => 'nullable|string|max:10',
            'badge_color'    => 'nullable|string|max:10',
            'type'           => 'required|in:streak,daily,quiz,test,chapter,competition,special',
            'threshold'      => 'required|integer|min:1',
        ]);

        $achievement->update([
            'title_en'       => $request->title_en,
            'title_np'       => $request->title_np,
            'description_en' => $request->description_en,
            'description_np' => $request->description_np,
            'icon'           => $request->icon,
            'badge_color'    => $request->badge_color ?? '#4A5D4A',
            'type'           => $request->type,
            'threshold'      => $request->threshold,
            'is_active'      => $request->has('is_active'),
        ]);

        return redirect()->route('admin.learning.achievements.index')
            ->with('success', 'Achievement updated.');
    }

    public function destroy(Achievement $achievement)
    {
        $achievement->delete();
        return back()->with('success', 'Achievement deleted.');
    }
}
