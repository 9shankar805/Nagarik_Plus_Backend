@extends('admin.layouts.app')
@section('title', 'Edit Flashcard Set')
@section('subtitle', $flashcard->title_en)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.learning.flashcards.index') }}" class="text-sm text-blue-600 hover:text-blue-700">← Back to Sets</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Edit: {{ $flashcard->title_en }}</h2>
</div>

<div class="grid grid-cols-3 gap-6">

    {{-- Set settings --}}
    <div class="col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
            <h3 class="font-semibold text-gray-800 border-b pb-2">Set Settings</h3>
            <form action="{{ route('admin.learning.flashcards.update', $flashcard) }}" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Title (EN) *</label>
                    <input type="text" name="title_en" value="{{ old('title_en', $flashcard->title_en) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Title (NP)</label>
                    <input type="text" name="title_np" value="{{ old('title_np', $flashcard->title_np) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                    <select name="learning_category_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        <option value="">— None —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('learning_category_id', $flashcard->learning_category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->icon }} {{ $cat->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Linked Chapter</label>
                    <select name="learning_chapter_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        <option value="">— None —</option>
                        @foreach($chapters as $ch)
                            <option value="{{ $ch->id }}" {{ old('learning_chapter_id', $flashcard->learning_chapter_id) == $ch->id ? 'selected' : '' }}>
                                {{ $ch->title_en }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Display Order</label>
                    <input type="number" name="display_order" value="{{ $flashcard->display_order }}" min="0"
                           class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_published" value="1" {{ $flashcard->is_published ? 'checked' : '' }}
                           class="w-4 h-4 text-[#4A5D4A] rounded border-gray-300">
                    <span class="text-sm font-medium text-gray-700">Published</span>
                </label>
                <button type="submit" class="w-full px-4 py-2.5 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] font-medium text-sm">
                    Update Set
                </button>
            </form>
        </div>
    </div>

    {{-- Cards list + add card --}}
    <div class="col-span-2 space-y-5">

        {{-- Add new card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 border-b pb-2 mb-4">Add New Card</h3>
            <form action="{{ route('admin.learning.flashcards.cards.store', $flashcard) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Front — Term (EN) *</label>
                        <textarea name="front_en" rows="2" required placeholder="Term, question, or concept..."
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Front — Term (NP)</label>
                        <textarea name="front_np" rows="2"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Back — Definition (EN) *</label>
                        <textarea name="back_en" rows="2" required placeholder="Answer, definition, explanation..."
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Back — Definition (NP)</label>
                        <textarea name="back_np" rows="2"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]"></textarea>
                    </div>
                </div>
                <div class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Image (optional)</label>
                        <input type="file" name="image_file" accept="image/*"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="w-24">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Order</label>
                        <input type="number" name="display_order" value="{{ $flashcard->card_count }}" min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                    <button type="submit"
                            class="px-5 py-2.5 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] font-medium text-sm">
                        + Add Card
                    </button>
                </div>
            </form>
        </div>

        {{-- Cards table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 border-b font-semibold text-gray-800 flex items-center justify-between">
                <span>Cards ({{ $flashcard->card_count }})</span>
            </div>
            <div class="divide-y divide-gray-100 max-h-[500px] overflow-y-auto">
                @forelse($cards as $card)
                <div class="p-4 hover:bg-gray-50">
                    <div class="grid grid-cols-2 gap-4 mb-2 text-sm">
                        <div>
                            <span class="text-xs font-bold text-blue-600 uppercase">Front</span>
                            <p class="text-gray-800 mt-0.5">{{ $card->front_en }}</p>
                            @if($card->front_np)<p class="text-xs text-gray-400">{{ $card->front_np }}</p>@endif
                        </div>
                        <div>
                            <span class="text-xs font-bold text-green-600 uppercase">Back</span>
                            <p class="text-gray-800 mt-0.5">{{ $card->back_en }}</p>
                            @if($card->back_np)<p class="text-xs text-gray-400">{{ $card->back_np }}</p>@endif
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <span class="text-xs text-gray-400 mr-auto">#{$card->display_order}</span>
                        <form action="{{ route('admin.learning.flashcards.cards.destroy', $card) }}" method="POST"
                              class="inline" onsubmit="return confirm('Delete this card?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Delete</button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-gray-400 text-sm">No cards yet. Add one above.</div>
                @endforelse
            </div>
            @if($cards->hasPages())
            <div class="p-4 border-t">{{ $cards->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
