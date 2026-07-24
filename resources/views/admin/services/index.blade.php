@extends('admin.layouts.app')

@section('title', 'Citizen Services')
@section('subtitle', 'Manage available citizen services')

@section('content')

<div class="flex justify-end mb-6">
    <a href="{{ route('admin.services.create') }}"
       class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
        + New Service
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sort</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($services as $service)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3">
                            <p class="font-medium text-gray-800">{{ $service->title }}</p>
                            <p class="text-xs text-gray-400 font-mono">{{ $service->slug }}</p>
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                                {{ ucfirst($service->category) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-center text-gray-600">{{ $service->sort_order }}</td>
                        <td class="px-6 py-3 text-center">
                            @if($service->is_active)
                                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Yes</span>
                            @else
                                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-500">No</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.services.edit', $service) }}"
                                   class="px-3 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 border border-blue-200 hover:border-blue-400 rounded-lg transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.services.destroy', $service) }}"
                                      onsubmit="return confirm('Delete this service?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1 text-xs font-medium text-red-600 hover:text-red-800 border border-red-200 hover:border-red-400 rounded-lg transition-colors">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">No services yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($services->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $services->links() }}
        </div>
    @endif
</div>

@endsection
