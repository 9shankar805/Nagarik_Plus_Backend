@extends('admin.layouts.app')

@section('title', 'Grievances (Hello Sarkar)')
@section('subtitle', 'Manage citizen grievances and reports')

@section('content')

<div class="mb-6 flex flex-wrap gap-4 items-center justify-between">
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.grievances.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border hover:bg-gray-50' }}">All</a>
        <a href="{{ route('admin.grievances.index', ['status' => 'open']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'open' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border hover:bg-gray-50' }}">Open</a>
        <a href="{{ route('admin.grievances.index', ['status' => 'in_progress']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'in_progress' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border hover:bg-gray-50' }}">In Progress</a>
        <a href="{{ route('admin.grievances.index', ['status' => 'resolved']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'resolved' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border hover:bg-gray-50' }}">Resolved</a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-4 font-medium">Ticket ID</th>
                    <th class="px-6 py-4 font-medium">Citizen</th>
                    <th class="px-6 py-4 font-medium">Category</th>
                    <th class="px-6 py-4 font-medium">Title</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                    <th class="px-6 py-4 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($grievances as $grievance)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 text-gray-900 font-medium">#{{ $grievance->id }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $grievance->user->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $grievance->category->name_en ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-gray-900 font-medium">{{ str($grievance->title)->limit(40) }}</td>
                        <td class="px-6 py-4">
                            @if($grievance->status === 'open')
                                <span class="px-2.5 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">Open</span>
                            @elseif($grievance->status === 'in_progress')
                                <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">In Progress</span>
                            @elseif($grievance->status === 'resolved')
                                <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Resolved</span>
                            @else
                                <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">{{ ucfirst($grievance->status) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.grievances.show', $grievance) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Review &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            No grievances found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($grievances->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $grievances->links() }}
        </div>
    @endif
</div>

@endsection
