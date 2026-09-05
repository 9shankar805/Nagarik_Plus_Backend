<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningCategory;
use App\Models\LearningChapter;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminLearningChapterController extends Controller
{
    public function index(Request $request)
    {
        $query = LearningChapter::with('category:id,name_en,slug');

        if ($request->category_id) {
            $query->where('learning_category_id', $request->category_id);
        }
        if ($request->content_type) {
            $query->where('content_type', $request->content_type);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title_en', 'like', '%' . $request->search . '%')
                  ->orWhere('title_np', 'like', '%' . $request->search . '%');
            });
        }

        $chapters   = $query->ordered()->paginate(20)->withQueryString();
        $categories = LearningCategory::active()->ordered()->get();

        return view('admin.learning.chapters.index', compact('chapters', 'categories'));
    }

    public function create()
    {
        $categories = LearningCategory::active()->ordered()->get();
        $subjects   = Subject::active()->orderBy('title_en')->get();
        $types      = LearningChapter::TYPES;
        return view('admin.learning.chapters.create', compact('categories', 'subjects', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'learning_category_id' => 'required|exists:learning_categories,id',
            'subject_id'           => 'nullable|exists:subjects,id',
            'title_en'             => 'required|string|max:255',
            'title_np'             => 'nullable|string|max:255',
            'content_type'         => 'required|in:lecture,video,note,model_set,audio',
            'duration_minutes'     => 'nullable|integer|min:1|max:600',
            'content_en'           => 'nullable|string',
            'content_np'           => 'nullable|string',
            'summary_en'           => 'nullable|string|max:1000',
            'summary_np'           => 'nullable|string|max:1000',
            'video_url'            => 'nullable|url|max:500',
            'read_time_minutes'    => 'nullable|integer|min:1|max:120',
            'display_order'        => 'nullable|integer|min:0',
            'image_file'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image_file')) {
            $path     = $request->file('image_file')->store('learning/chapters', 'public');
            $imageUrl = Storage::url($path);
        }

        $isPublished = $request->has('is_published');

        $chapter = LearningChapter::create([
            'learning_category_id' => $request->learning_category_id,
            'subject_id'           => $request->subject_id ?: null,
            'title_en'             => $request->title_en,
            'title_np'             => $request->title_np,
            'content_type'         => $request->content_type,
            'duration_minutes'     => $request->duration_minutes,
            'content_en'           => $request->content_en,
            'content_np'           => $request->content_np,
            'summary_en'           => $request->summary_en,
            'summary_np'           => $request->summary_np,
            'image_url'            => $imageUrl,
            'video_url'            => $request->video_url,
            'read_time_minutes'    => $request->read_time_minutes ?? 5,
            'display_order'        => $request->display_order ?? 0,
            'is_published'         => $isPublished,
            'published_at'         => $isPublished ? now() : null,
        ]);

        // Sync counts on the parent subject if assigned
        if ($chapter->subject_id) {
            $chapter->subject->syncAllCounts();
        }

        return redirect()->route('admin.learning.chapters.index')
            ->with('success', 'Chapter created successfully.');
    }

    public function edit(LearningChapter $chapter)
    {
        $categories = LearningCategory::active()->ordered()->get();
        $subjects   = Subject::active()->orderBy('title_en')->get();
        $types      = LearningChapter::TYPES;
        return view('admin.learning.chapters.edit', compact('chapter', 'categories', 'subjects', 'types'));
    }

    public function update(Request $request, LearningChapter $chapter)
    {
        $request->validate([
            'learning_category_id' => 'required|exists:learning_categories,id',
            'subject_id'           => 'nullable|exists:subjects,id',
            'title_en'             => 'required|string|max:255',
            'title_np'             => 'nullable|string|max:255',
            'content_type'         => 'required|in:lecture,video,note,model_set,audio',
            'duration_minutes'     => 'nullable|integer|min:1|max:600',
            'content_en'           => 'nullable|string',
            'content_np'           => 'nullable|string',
            'summary_en'           => 'nullable|string|max:1000',
            'summary_np'           => 'nullable|string|max:1000',
            'video_url'            => 'nullable|url|max:500',
            'read_time_minutes'    => 'nullable|integer|min:1|max:120',
            'display_order'        => 'nullable|integer|min:0',
            'image_file'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $imageUrl = $chapter->image_url;
        if ($request->hasFile('image_file')) {
            $path     = $request->file('image_file')->store('learning/chapters', 'public');
            $imageUrl = Storage::url($path);
        }

        $isPublished = $request->has('is_published');
        $publishedAt = $chapter->published_at;
        if ($isPublished && !$chapter->is_published) {
            $publishedAt = now();
        }

        $oldSubjectId = $chapter->subject_id;

        $chapter->update([
            'learning_category_id' => $request->learning_category_id,
            'subject_id'           => $request->subject_id ?: null,
            'title_en'             => $request->title_en,
            'title_np'             => $request->title_np,
            'content_type'         => $request->content_type,
            'duration_minutes'     => $request->duration_minutes,
            'content_en'           => $request->content_en,
            'content_np'           => $request->content_np,
            'summary_en'           => $request->summary_en,
            'summary_np'           => $request->summary_np,
            'image_url'            => $imageUrl,
            'video_url'            => $request->video_url,
            'read_time_minutes'    => $request->read_time_minutes ?? 5,
            'display_order'        => $request->display_order ?? 0,
            'is_published'         => $isPublished,
            'published_at'         => $publishedAt,
        ]);

        // Sync old subject if it changed
        if ($oldSubjectId && $oldSubjectId !== $chapter->subject_id) {
            Subject::find($oldSubjectId)?->syncAllCounts();
        }
        if ($chapter->subject_id) {
            $chapter->subject->syncAllCounts();
        }

        return redirect()->route('admin.learning.chapters.index')
            ->with('success', 'Chapter updated successfully.');
    }

    public function destroy(LearningChapter $chapter)
    {
        $subjectId = $chapter->subject_id;
        $chapter->delete();

        if ($subjectId) {
            Subject::find($subjectId)?->syncAllCounts();
        }

        return back()->with('success', 'Chapter deleted.');
    }
}
