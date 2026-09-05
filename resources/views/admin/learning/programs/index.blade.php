@extends('admin.layouts.app')
@section('title', 'Programs')
@section('subtitle', 'Top-level learning programs (Driving License, Loksewa, Banking, etc.)')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Learning Programs</h2>
        <p class="text-sm text-gray-500 mt-1">Each program contains Courses → Subjects → Chapters hierarchy.</p>
    </div>
    <a href="{{ route('admin.learning.programs.create') }}"
       class="px-4 py-2 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] font-medium text-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Program
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 border-b text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Order</th>
                <th class="py-3 px-4">Program</th>
                <th class="py-3 px-4">Category</th>
                <th class="py-3 px-4 text-center">Courses</th>
                <th class="py-3 px-4 text-center">Enrolled</th>
                <th class="py-3 px-4 text-center">Price</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($programs as $prog)
            <tr class="hover:bg-gray-50">
                <td class="py-3 px-4 text-gray-400 font-mono text-xs">{{ $prog->display_order }}</td>
                <td class="py-3 px-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">{{ $prog->icon ?? '📚' }}</span>
                        <div>
                            <div class="font-semibold text-gray-900">{{ $prog->title_en }}</div>
                            @if($prog->title_np)<div class="text-xs text-gray-400">{{ $prog->title_np }}</div>@endif
                        </div>
                    </div>
                </td>
                <td class="py-3 px-4">
                    @if($prog->category)
                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs">{{ $prog->category->icon }} {{ $prog->category->name_en }}</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-center">
                    <span class="px-2.5 py-1 bg-purple-50 text-purple-700 rounded-full text-xs font-bold">{{ $prog->courses_count }}</span>
                </td>
                <td class="py-3 px-4 text-center text-gray-600 font-semibold">{{ number_format($prog->enrolled_count) }}</td>
                <td class="py-3 px-4 text-center">
                    @if($prog->is_free)
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Free</span>
                    @else
                        <span class="text-sm font-semibold text-gray-700">NPR {{ number_format($prog->price) }}</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-center">
                    @if($prog->is_published)
                        <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Published</span>
                    @else
                        <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">Draft</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-right space-x-3">
                    <a href="{{ route('admin.learning.programs.show', $prog) }}" class="text-purple-600 hover:text-purple-800 font-medium">Manage</a>
                    <a href="{{ route('admin.learning.programs.edit', $prog) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                    <form action="{{ route('admin.learning.programs.destroy', $prog) }}" method="POST" class="inline"
                          onsubmit="return confirm('Delete this program and all its courses?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Del</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="py-10 text-center text-gray-400">No programs yet. <a href="{{ route('admin.learning.programs.create') }}" class="text-blue-600 hover:underline">Create one</a>.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t">{{ $programs->links() }}</div>
</div>
@endsection
