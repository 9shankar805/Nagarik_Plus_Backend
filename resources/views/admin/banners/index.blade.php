@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Home Carousel Banners</h1>
        <p class="text-sm text-gray-600">Manage interactive banner slides displayed on the mobile app home screen.</p>
    </div>
    <a href="{{ route('admin.banners.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm flex items-center gap-2">
        <span>+ Add New Banner</span>
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Order</th>
                <th class="py-3 px-4">Title (EN / NP)</th>
                <th class="py-3 px-4">Description</th>
                <th class="py-3 px-4">Link Type</th>
                <th class="py-3 px-4">Schedule</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($banners as $banner)
            <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4 text-gray-600 font-mono">{{ $banner->sort_order }}</td>
                <td class="py-3 px-4 font-semibold text-gray-900">
                    <div>{{ $banner->title }}</div>
                    <div class="text-xs text-gray-500 font-normal">{{ $banner->title_np }}</div>
                </td>
                <td class="py-3 px-4 text-gray-600 max-w-xs truncate">{{ $banner->description ?? '-' }}</td>
                <td class="py-3 px-4">
                    <span class="px-2 py-1 text-xs font-medium rounded bg-blue-50 text-blue-700">{{ ucfirst($banner->link_type) }}</span>
                    @if($banner->link_value)
                        <div class="text-xs text-gray-500 mt-1 truncate max-w-[120px]">{{ $banner->link_value }}</div>
                    @endif
                </td>
                <td class="py-3 px-4 text-xs text-gray-600">
                    @if($banner->starts_at || $banner->ends_at)
                        <div>{{ $banner->starts_at ? $banner->starts_at->format('M j, Y') : '—' }} → {{ $banner->ends_at ? $banner->ends_at->format('M j, Y') : '∞' }}</div>
                    @else
                        <span class="text-gray-400">Always</span>
                    @endif
                </td>
                <td class="py-3 px-4">
                    @if($banner->is_active)
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Inactive</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-right space-x-2">
                    <a href="{{ route('admin.banners.edit', $banner) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                    <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="inline" onsubmit="return confirm('Delete this banner?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-8 text-center text-gray-500">No banners found. Click "+ Add New Banner" to create one.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">
        {{ $banners->links() }}
    </div>
</div>
@endsection
