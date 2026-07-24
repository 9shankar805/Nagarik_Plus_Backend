@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Educational Video Shorts</h1>
        <p class="text-sm text-gray-600">Upload video shorts, driving license tutorials, and traffic rule clips for mobile users.</p>
    </div>
    <a href="{{ route('admin.shorts.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm flex items-center gap-2">
        <span>+ Upload Video Short</span>
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Title (EN / NP)</th>
                <th class="py-3 px-4">Category</th>
                <th class="py-3 px-4">Video Link / Asset</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($shorts as $short)
            <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4 font-semibold text-gray-900">
                    <div>{{ $short->title_en }}</div>
                    <div class="text-xs text-gray-500 font-normal">{{ $short->title_np }}</div>
                </td>
                <td class="py-3 px-4">
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                        {{ ucfirst(str_replace('_', ' ', $short->category)) }}
                    </span>
                </td>
                <td class="py-3 px-4 text-gray-600 text-xs font-mono">
                    <a href="{{ $short->video_url }}" target="_blank" class="text-blue-600 underline">Preview Video 🎬</a>
                </td>
                <td class="py-3 px-4">
                    @if($short->is_published)
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Draft</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-right space-x-2">
                    <a href="{{ route('admin.shorts.edit', $short) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                    <form action="{{ route('admin.shorts.destroy', $short) }}" method="POST" class="inline" onsubmit="return confirm('Delete this video short?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-8 text-center text-gray-500">No video shorts uploaded yet. Click "+ Upload Video Short" to upload one.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">
        {{ $shorts->links() }}
    </div>
</div>
@endsection
