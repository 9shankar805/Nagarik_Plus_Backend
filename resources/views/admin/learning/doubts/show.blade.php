@extends('admin.layouts.app')

@section('title', 'Doubt Detail')
@section('subtitle', 'View, answer and moderate this student question')

@section('content')
<div class="mb-6 flex justify-between items-start">
    <div>
        <a href="{{ route('admin.learning.doubts.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Doubts</a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">{{ $doubt->title }}</h2>
    </div>
    <div class="flex gap-2">
        {{-- Pin toggle --}}
        <form action="{{ route('admin.learning.doubts.pin', $doubt) }}" method="POST">
            @csrf @method('PATCH')
            <button type="submit"
                    class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 hover:bg-gray-100 {{ $doubt->is_pinned ? 'bg-yellow-50 border-yellow-300 text-yellow-700' : 'text-gray-600' }}">
                {{ $doubt->is_pinned ? '📌 Unpin' : '📌 Pin' }}
            </button>
        </form>
        {{-- Close / Reopen --}}
        @if($doubt->status !== 'closed')
        <form action="{{ route('admin.learning.doubts.close', $doubt) }}" method="POST">
            @csrf @method('PATCH')
            <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-red-200 text-red-600 hover:bg-red-50">
                Close Doubt
            </button>
        </form>
        @else
        <form action="{{ route('admin.learning.doubts.reopen', $doubt) }}" method="POST">
            @csrf @method('PATCH')
            <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-green-200 text-green-600 hover:bg-green-50">
                Reopen
            </button>
        </form>
        @endif
        {{-- Delete --}}
        <form action="{{ route('admin.learning.doubts.destroy', $doubt) }}" method="POST"
              onsubmit="return confirm('Delete this doubt and all answers?')">
            @csrf @method('DELETE')
            <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-red-200 text-red-600 hover:bg-red-50">
                Delete
            </button>
        </form>
    </div>
</div>

<div class="grid grid-cols-3 gap-6">

    {{-- Question + Answers --}}
    <div class="col-span-2 space-y-5">

        {{-- Original question --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-start gap-3 mb-4">
                <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-sm shrink-0">
                    {{ strtoupper(substr($doubt->user?->name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <div class="font-semibold text-gray-900">{{ $doubt->user?->name ?? 'Unknown' }}</div>
                    <div class="text-xs text-gray-400">{{ $doubt->created_at->format('d M Y, H:i') }}</div>
                </div>
                <div class="ml-auto flex gap-2">
                    @if($doubt->category)
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                            {{ $doubt->category->icon }} {{ $doubt->category->name_en }}
                        </span>
                    @endif
                    @if($doubt->chapter)
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs">
                            {{ $doubt->chapter->title_en }}
                        </span>
                    @endif
                </div>
            </div>

            <p class="text-gray-700 leading-relaxed">{{ $doubt->body }}</p>

            @if($doubt->image_url)
                <img src="{{ $doubt->image_url }}" alt="Question image"
                     class="mt-4 max-w-sm rounded-lg border border-gray-200">
            @endif
        </div>

        {{-- Existing answers --}}
        @if($doubt->answers->count())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">{{ $doubt->answers->count() }} Answer(s)</h3>
            <div class="space-y-5 divide-y divide-gray-100">
                @foreach($doubt->answers as $ans)
                <div class="pt-4 first:pt-0 flex gap-3">
                    <div class="w-8 h-8 rounded-full {{ $ans->is_instructor ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center text-xs font-bold shrink-0 {{ $ans->is_instructor ? 'text-green-700' : 'text-gray-600' }}">
                        {{ strtoupper(substr($ans->user?->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-semibold text-gray-800 text-sm">{{ $ans->user?->name ?? 'Deleted User' }}</span>
                            @if($ans->is_instructor)
                                <span class="px-1.5 py-0.5 bg-green-100 text-green-700 text-xs rounded font-medium">Instructor</span>
                            @endif
                            @if($ans->is_accepted)
                                <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 text-xs rounded font-medium">✓ Accepted</span>
                            @endif
                            <span class="text-xs text-gray-400 ml-auto">{{ $ans->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <p class="text-gray-700 text-sm leading-relaxed">{{ $ans->body }}</p>
                        @if($ans->image_url)
                            <img src="{{ $ans->image_url }}" alt="" class="mt-2 max-w-xs rounded border border-gray-200">
                        @endif
                        <div class="mt-2">
                            <form action="{{ route('admin.learning.doubts.answer.destroy', $ans) }}" method="POST"
                                  class="inline" onsubmit="return confirm('Delete this answer?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Delete answer</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Post answer form --}}
        @if($doubt->status !== 'closed')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Post Official Answer</h3>
            <form action="{{ route('admin.learning.doubts.answer', $doubt) }}" method="POST">
                @csrf
                <textarea name="body" rows="5" required placeholder="Write a clear, helpful answer for the student..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] @error('body') border-red-400 @enderror">{{ old('body') }}</textarea>
                @error('body')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                <div class="mt-3 flex gap-3">
                    <button type="submit"
                            class="px-5 py-2 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] transition font-medium text-sm">
                        Post Answer (as Instructor)
                    </button>
                </div>
            </form>
        </div>
        @else
        <div class="bg-gray-50 rounded-xl border border-gray-200 p-5 text-center text-sm text-gray-500">
            This doubt is <strong>closed</strong>. Reopen it to post an answer.
        </div>
        @endif

    </div>

    {{-- Sidebar: meta --}}
    <div class="col-span-1 space-y-5">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-3">
            <h3 class="font-semibold text-gray-800 border-b pb-2">Details</h3>
            <div class="text-sm space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-500">Status</span>
                    @if($doubt->status === 'open')
                        <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs rounded-full font-semibold">Open</span>
                    @elseif($doubt->status === 'answered')
                        <span class="px-2 py-0.5 bg-green-100 text-green-800 text-xs rounded-full font-semibold">Answered</span>
                    @else
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-xs rounded-full font-semibold">Closed</span>
                    @endif
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Upvotes</span>
                    <span class="font-medium text-gray-800">{{ $doubt->upvotes }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Answers</span>
                    <span class="font-medium text-gray-800">{{ $doubt->answers_count }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Pinned</span>
                    <span class="font-medium text-gray-800">{{ $doubt->is_pinned ? 'Yes 📌' : 'No' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Asked on</span>
                    <span class="font-medium text-gray-800 text-xs">{{ $doubt->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-2">
            <h3 class="font-semibold text-gray-800 border-b pb-2">Student</h3>
            <div class="text-sm">
                <p class="font-medium text-gray-900">{{ $doubt->user?->name ?? '—' }}</p>
                <p class="text-gray-400 text-xs">{{ $doubt->user?->email }}</p>
            </div>
        </div>
    </div>

</div>
@endsection
