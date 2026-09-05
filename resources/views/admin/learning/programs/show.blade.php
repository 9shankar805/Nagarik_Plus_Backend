@extends('admin.layouts.app')
@section('title', 'Manage Program')
@section('subtitle', $program->title_en)

@section('content')
<div class="mb-6 flex items-start justify-between">
    <div>
        <a href="{{ route('admin.learning.programs.index') }}" class="text-sm text-blue-600 hover:text-blue-700">← Back to Programs</a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">{{ $program->icon }} {{ $program->title_en }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $program->category?->name_en }} · {{ $program->is_free ? 'Free' : 'NPR '.$program->price }}</p>
    </div>
    <div class="flex gap-2 mt-4">
        <a href="{{ route('admin.learning.programs.edit', $program) }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Edit Program</a>
        <a href="{{ route('admin.learning.courses.create', $program) }}"
           class="px-4 py-2 bg-[#4A5D4A] text-white rounded-lg text-sm font-medium hover:bg-[#3F523F]">+ Add Course</a>
    </div>
</div>

@if($program->courses->isEmpty())
<div class="bg-white rounded-xl border border-dashed border-gray-200 p-12 text-center text-gray-400">
    <p class="text-lg mb-2">No courses yet.</p>
    <a href="{{ route('admin.learning.courses.create', $program) }}" class="text-blue-600 hover:underline">Add the first course</a>
</div>
@else
<div class="space-y-6">
    @foreach($program->courses as $course)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Course header --}}
        <div class="px-5 py-4 bg-gray-50 border-b flex items-center justify-between">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">{{ $course->title_en }}</h3>
                @if($course->title_np)<p class="text-sm text-gray-400">{{ $course->title_np }}</p>@endif
                <div class="flex gap-3 mt-1 text-xs text-gray-500">
                    <span>{{ $course->total_subjects }} subjects</span>
                    <span>{{ $course->total_chapters }} chapters</span>
                    @if($course->is_published)
                        <span class="text-green-600 font-semibold">● Published</span>
                    @else
                        <span class="text-yellow-600 font-semibold">● Draft</span>
                    @endif
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.learning.courses.edit', $course) }}"
                   class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-medium hover:bg-blue-100">Edit Course</a>
                <form action="{{ route('admin.learning.courses.destroy', $course) }}" method="POST" class="inline"
                      onsubmit="return confirm('Delete this course?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-medium hover:bg-red-100">Delete</button>
                </form>
            </div>
        </div>

        {{-- Subjects list --}}
        <div class="p-5">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold text-gray-700 text-sm">Subjects</h4>
            </div>

            @if($course->subjects->isEmpty())
            <p class="text-sm text-gray-400 mb-3">No subjects yet.</p>
            @else
            <div class="grid grid-cols-2 gap-2 mb-4">
                @foreach($course->subjects as $subj)
                <div class="flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2">
                    <div class="flex items-center gap-2">
                        <span class="text-base">{{ $subj->icon ?? '📖' }}</span>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $subj->title_en }}</p>
                            <p class="text-xs text-gray-400">{{ $subj->chapter_count }} chapters</p>
                        </div>
                    </div>
                    <form action="{{ route('admin.learning.subjects.destroy', $subj) }}" method="POST" class="inline"
                          onsubmit="return confirm('Delete subject?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-400 hover:text-red-600">✕</button>
                    </form>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Add subject inline form --}}
            <details class="mt-2">
                <summary class="text-sm text-blue-600 cursor-pointer hover:text-blue-800 font-medium">+ Add Subject</summary>
                <form action="{{ route('admin.learning.subjects.store', $course) }}" method="POST"
                      class="mt-3 bg-blue-50 rounded-lg p-4 space-y-3">
                    @csrf
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-2">
                            <input type="text" name="title_en" placeholder="Subject name (EN) *" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        </div>
                        <div>
                            <input type="text" name="title_np" placeholder="नेपाली नाम"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <input type="text" name="icon" placeholder="Icon 📝"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        </div>
                        <div>
                            <input type="color" name="color_code" value="#4A5D4A"
                                   class="h-10 w-full border border-gray-300 rounded-lg cursor-pointer">
                        </div>
                        <div>
                            <input type="number" name="display_order" placeholder="Order" min="0"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        </div>
                    </div>
                    <div class="flex gap-3 items-center">
                        <label class="flex items-center gap-1 text-sm">
                            <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-[#4A5D4A] rounded">
                            Active
                        </label>
                        <button type="submit" class="px-4 py-2 bg-[#4A5D4A] text-white rounded-lg text-sm font-medium hover:bg-[#3F523F]">
                            Add Subject
                        </button>
                    </div>
                </form>
            </details>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
