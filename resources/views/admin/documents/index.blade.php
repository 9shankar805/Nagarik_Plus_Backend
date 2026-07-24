@extends('admin.layouts.app')

@section('title', 'Documents')
@section('subtitle', 'All user documents')

@section('content')

{{-- Filters --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" action="{{ route('admin.documents.index') }}" class="flex gap-3 flex-wrap">
        <select name="type"
                class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
            <option value="">All Types</option>
            @foreach(['citizenship', 'passport', 'license', 'voter_id', 'pan', 'birth_certificate', 'vehicle', 'other'] as $t)
                <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $t)) }}</option>
            @endforeach
        </select>
        <select name="status"
                class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
            <option value="">All Statuses</option>
            @foreach(['active', 'expired', 'expiring_soon'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $s)) }}</option>
            @endforeach
        </select>
        <button type="submit"
                class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
            Filter
        </button>
        @if(request()->hasAny(['type','status']))
            <a href="{{ route('admin.documents.index') }}"
               class="px-5 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                Clear
            </a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <p class="text-sm text-gray-600">
            Showing <span class="font-semibold">{{ $documents->firstItem() }}–{{ $documents->lastItem() }}</span>
            of <span class="font-semibold">{{ $documents->total() }}</span> documents
        </p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">File?</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($documents as $doc)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3">
                            <p class="font-medium text-gray-800">{{ optional($doc->user)->name ?? '—' }}</p>
                            <p class="text-xs text-gray-500">{{ optional($doc->user)->email ?? '' }}</p>
                        </td>
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $doc->title }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ ucwords(str_replace('_', ' ', $doc->type ?? '')) }}</td>
                        <td class="px-6 py-3">
                            <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full
                                {{ $doc->status === 'active' ? 'bg-green-100 text-green-700' :
                                   ($doc->status === 'expired' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst($doc->status ?? 'active') }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-500">
                            {{ $doc->expiry_date ? $doc->expiry_date->format('d M Y') : '—' }}
                        </td>
                        <td class="px-6 py-3 text-center">
                            @if($doc->file_name)
                                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Yes</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">No documents found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($documents->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $documents->links() }}
        </div>
    @endif
</div>

@endsection
