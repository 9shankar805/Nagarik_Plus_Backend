<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Flashcard;
use App\Models\FlashcardSet;
use App\Models\LearningCategory;
use App\Models\LearningChapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminFlashcardController extends Controller
{
    // ── Sets ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = FlashcardSet::with('category:id,name_en,icon');
        if ($request->category_id) {
            $query->where('learning_category_id', $request->category_id);
        }
        $sets       = $query->orderBy('display_order')->paginate(20)->withQueryString();
        $categories = LearningCategory::active()->ordered()->get();

        return view('admin.learning.flashcards.index', compact('sets', 'categories'));
    }

    public function create()
    {
        $categories = LearningCategory::active()->ordered()->get();
        $chapters   = LearningChapter::published()->select('id', 'title_en', 'learning_category_id')->get();

        return view('admin.learning.flashcards.create', compact('categories', 'chapters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title_en'             => 'required|string|max:255',
            'title_np'             => 'nullable|string|max:255',
            'description'          => 'nullable|string',
            'learning_category_id' => 'nullable|exists:learning_categories,id',
            'learning_chapter_id'  => 'nullable|exists:learning_chapters,id',
            'display_order'        => 'nullable|integer|min:0',
        ]);

        FlashcardSet::create([
            'title_en'             => $request->title_en,
            'title_np'             => $request->title_np,
            'description'          => $request->description,
            'learning_category_id' => $request->learning_category_id ?: null,
            'learning_chapter_id'  => $request->learning_chapter_id ?: null,
            'display_order'        => $request->display_order ?? 0,
            'is_published'         => $request->has('is_published'),
        ]);

        return redirect()->route('admin.learning.flashcards.index')
            ->with('success', 'Flashcard set created.');
    }

    public function edit(FlashcardSet $flashcard)
    {
        $categories = LearningCategory::active()->ordered()->get();
        $chapters   = LearningChapter::published()->select('id', 'title_en', 'learning_category_id')->get();
        $cards      = $flashcard->allCards()->paginate(20);

        return view('admin.learning.flashcards.edit', compact('flashcard', 'categories', 'chapters', 'cards'));
    }

    public function update(Request $request, FlashcardSet $flashcard)
    {
        $request->validate([
            'title_en'             => 'required|string|max:255',
            'title_np'             => 'nullable|string|max:255',
            'description'          => 'nullable|string',
            'learning_category_id' => 'nullable|exists:learning_categories,id',
            'learning_chapter_id'  => 'nullable|exists:learning_chapters,id',
            'display_order'        => 'nullable|integer|min:0',
        ]);

        $flashcard->update([
            'title_en'             => $request->title_en,
            'title_np'             => $request->title_np,
            'description'          => $request->description,
            'learning_category_id' => $request->learning_category_id ?: null,
            'learning_chapter_id'  => $request->learning_chapter_id ?: null,
            'display_order'        => $request->display_order ?? 0,
            'is_published'         => $request->has('is_published'),
        ]);

        return redirect()->route('admin.learning.flashcards.index')
            ->with('success', 'Flashcard set updated.');
    }

    public function destroy(FlashcardSet $flashcard)
    {
        $flashcard->delete();
        return back()->with('success', 'Flashcard set deleted.');
    }

    // ── Individual Cards ────────────────────────────────────────────────────

    public function storeCard(Request $request, FlashcardSet $flashcard)
    {
        $request->validate([
            'front_en'     => 'required|string',
            'front_np'     => 'nullable|string',
            'back_en'      => 'required|string',
            'back_np'      => 'nullable|string',
            'display_order'=> 'nullable|integer|min:0',
            'image_file'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image_file')) {
            $path     = $request->file('image_file')->store('learning/flashcards', 'public');
            $imageUrl = Storage::url($path);
        }

        Flashcard::create([
            'flashcard_set_id' => $flashcard->id,
            'front_en'         => $request->front_en,
            'front_np'         => $request->front_np,
            'back_en'          => $request->back_en,
            'back_np'          => $request->back_np,
            'image_url'        => $imageUrl,
            'display_order'    => $request->display_order ?? 0,
            'is_active'        => true,
        ]);

        $flashcard->syncCardCount();

        return back()->with('success', 'Card added successfully.');
    }

    public function updateCard(Request $request, Flashcard $card)
    {
        $request->validate([
            'front_en'      => 'required|string',
            'front_np'      => 'nullable|string',
            'back_en'       => 'required|string',
            'back_np'       => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'image_file'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imageUrl = $card->image_url;
        if ($request->hasFile('image_file')) {
            $path     = $request->file('image_file')->store('learning/flashcards', 'public');
            $imageUrl = Storage::url($path);
        }

        $card->update([
            'front_en'      => $request->front_en,
            'front_np'      => $request->front_np,
            'back_en'       => $request->back_en,
            'back_np'       => $request->back_np,
            'image_url'     => $imageUrl,
            'display_order' => $request->display_order ?? $card->display_order,
            'is_active'     => $request->has('is_active'),
        ]);

        $card->set->syncCardCount();

        return back()->with('success', 'Card updated.');
    }

    public function destroyCard(Flashcard $card)
    {
        $set = $card->set;
        $card->delete();
        $set->syncCardCount();

        return back()->with('success', 'Card deleted.');
    }
}
