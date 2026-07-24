@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Vital Event Cards</h1>
        <p class="text-sm text-gray-600">Manage birth, marriage, death, and migration event cards on the mobile app home screen.</p>
    </div>
    <a href="{{ route('admin.vital-events.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm flex items-center gap-2">
        <span>+ Add Event Card</span>
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Title (EN / NP)</th>
                <th class="py-3 px-4">Background Color</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($events as $event)
            <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4 font-semibold text-gray-900">
                    <div>{{ $event->title_en }}</div>
                    <div class="text-xs text-gray-500 font-normal">{{ $event->title_np }}</div>
                </td>
                <td class="py-3 px-4 text-gray-600">
                    <span class="px-2 py-1 rounded text-xs font-mono bg-gray-100">{{ $event->bg_color ?? 'default' }}</span>
                </td>
                <td class="py-3 px-4">
                    @if($event->is_active)
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Inactive</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-right space-x-2">
                    <a href="{{ route('admin.vital-events.edit', $event) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                    <form action="{{ route('admin.vital-events.destroy', $event) }}" method="POST" class="inline" onsubmit="return confirm('Delete this event card?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="py-8 text-center text-gray-500">No vital event cards configured. Click "+ Add Event Card" to create one.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">
        {{ $events->links() }}
    </div>
</div>
@endsection
