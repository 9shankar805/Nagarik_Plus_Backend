<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doubt;
use App\Models\DoubtAnswer;
use App\Models\LearningCategory;
use Illuminate\Http\Request;

class AdminDoubtController extends Controller
{
    public function index(Request $request)
    {
        $query = Doubt::with([
            'user:id,name,email,avatar',
            'category:id,name_en,icon',
        ]);

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->category_id) {
            $query->where('learning_category_id', $request->category_id);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('body', 'like', '%' . $request->search . '%');
            });
        }

        $doubts     = $query->latest()->paginate(20)->withQueryString();
        $categories = LearningCategory::active()->ordered()->get();

        $totalOpen     = Doubt::where('status', Doubt::STATUS_OPEN)->count();
        $totalAnswered = Doubt::where('status', Doubt::STATUS_ANSWERED)->count();

        return view('admin.learning.doubts.index', compact(
            'doubts', 'categories', 'totalOpen', 'totalAnswered'
        ));
    }

    public function show(Doubt $doubt)
    {
        $doubt->load([
            'user:id,name,email,avatar',
            'category:id,name_en,icon',
            'chapter:id,title_en',
            'answers.user:id,name,email,avatar',
        ]);

        return view('admin.learning.doubts.show', compact('doubt'));
    }

    /** Admin posts an official answer on behalf of an instructor */
    public function answer(Request $request, Doubt $doubt)
    {
        $request->validate([
            'body' => 'required|string|min:5',
        ]);

        $answer = DoubtAnswer::create([
            'doubt_id'      => $doubt->id,
            'user_id'       => auth()->id(),
            'body'          => $request->body,
            'is_instructor' => true,
            'is_accepted'   => false,
            'upvotes'       => 0,
        ]);

        // Update doubt status to answered and increment count
        $doubt->increment('answers_count');
        $doubt->update(['status' => Doubt::STATUS_ANSWERED]);

        return back()->with('success', 'Answer posted successfully.');
    }

    /** Pin / unpin a doubt */
    public function togglePin(Doubt $doubt)
    {
        $doubt->update(['is_pinned' => !$doubt->is_pinned]);
        return back()->with('success', $doubt->is_pinned ? 'Doubt pinned.' : 'Doubt unpinned.');
    }

    /** Close a doubt */
    public function close(Doubt $doubt)
    {
        $doubt->update(['status' => Doubt::STATUS_CLOSED]);
        return back()->with('success', 'Doubt closed.');
    }

    /** Reopen a closed doubt */
    public function reopen(Doubt $doubt)
    {
        $doubt->update(['status' => Doubt::STATUS_OPEN]);
        return back()->with('success', 'Doubt reopened.');
    }

    /** Delete a doubt and all its answers */
    public function destroy(Doubt $doubt)
    {
        $doubt->answers()->delete();
        $doubt->delete();
        return redirect()->route('admin.learning.doubts.index')
            ->with('success', 'Doubt deleted.');
    }

    /** Delete a single answer */
    public function destroyAnswer(DoubtAnswer $answer)
    {
        $doubt = $answer->doubt;
        $answer->delete();

        // Decrement count, floor at 0
        if ($doubt && $doubt->answers_count > 0) {
            $doubt->decrement('answers_count');
        }

        return back()->with('success', 'Answer deleted.');
    }
}
