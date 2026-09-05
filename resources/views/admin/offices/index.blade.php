@extends('admin.layouts.app')

@section('title', 'Offices')
@section('subtitle', 'Manage government offices')

@section('content')

<div class="flex justify-end mb-6">
    <a href="{{ route('admin.offices.create') }}"
       class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
        + New Office
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">District</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Province</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone / Email</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($offices as $office)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $office->name }}</td>
                        <td class="px-6 py-3">
                            <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                                {{ ucfirst($office->category) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $office->district }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $office->province }}</td>
                        <td class="px-6 py-3">
                            <div class="text-sm">
                                @if($office->phone)
                                    <div class="text-gray-800"><span class="text-gray-500 text-xs">P:</span> {{ $office->phone }}</div>
                                @else
                                    <div class="text-red-500 text-xs font-medium">No Phone</div>
                                @endif
                                @if($office->email)
                                    <div class="text-gray-800 mt-1"><span class="text-gray-500 text-xs">E:</span> {{ $office->email }}</div>
                                @else
                                    <div class="text-red-500 text-xs font-medium mt-1">No Email</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-3 text-center">
                            @if($office->latitude && $office->longitude)
                                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-green-50 text-green-700 border border-green-200" title="{{ $office->latitude }}, {{ $office->longitude }}">Mapped</span>
                            @else
                                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-red-50 text-red-700 border border-red-200">Unmapped</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-center">
                            @if($office->is_active)
                                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Yes</span>
                            @else
                                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-500">No</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.offices.edit', $office) }}"
                                   class="px-3 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 border border-blue-200 hover:border-blue-400 rounded-lg transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.offices.destroy', $office) }}"
                                      onsubmit="return confirm('Delete this office?')" class="inline">
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
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">No offices yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($offices->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $offices->links() }}
        </div>
    @endif
</div>

@endsection
