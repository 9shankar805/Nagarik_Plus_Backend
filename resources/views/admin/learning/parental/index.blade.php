@extends('admin.layouts.app')

@section('title', 'Parental Monitoring')
@section('subtitle', 'Manage parent–student links and monitor student activity')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Parental Monitoring</h2>
        <p class="text-sm text-gray-500 mt-1">All parent-student links created via the app.</p>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-3 gap-5 mb-7">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-gray-800">{{ $totalLinks }}</div>
        <div class="text-sm text-gray-500 mt-1">Total Links</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-green-600">{{ $active }}</div>
        <div class="text-sm text-gray-500 mt-1">Active Links</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-yellow-500">{{ $pending }}</div>
        <div class="text-sm text-gray-500 mt-1">Pending Approval</div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="flex gap-3 mb-5">
    <select name="status" onchange="this.form.submit()"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
        <option value="">All Status</option>
        <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>Active</option>
        <option value="pending"  {{ request('status') == 'pending'  ? 'selected' : '' }}>Pending</option>
        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
    </select>
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search parent or student name/email..."
           class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] w-72">
    <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">Search</button>
    @if(request()->hasAny(['status','search']))
        <a href="{{ route('admin.learning.parental.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">Clear</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Parent</th>
                <th class="py-3 px-4">Student</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4">Linked On</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($links as $link)
            <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4">
                    <div class="font-medium text-gray-900">{{ $link->parent?->name ?? '—' }}</div>
                    <div class="text-xs text-gray-400">{{ $link->parent?->email }}</div>
                    <div class="text-xs text-gray-400">{{ $link->parent?->phone }}</div>
                </td>
                <td class="py-3 px-4">
                    <div class="font-medium text-gray-900">{{ $link->student?->name ?? '—' }}</div>
                    <div class="text-xs text-gray-400">{{ $link->student?->email }}</div>
                    <div class="text-xs text-gray-400">{{ $link->student?->phone }}</div>
                </td>
                <td class="py-3 px-4 text-center">
                    @if($link->status === 'active')
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    @elseif($link->status === 'pending')
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">{{ ucfirst($link->status) }}</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-gray-400 text-xs">{{ $link->created_at->format('d M Y') }}</td>
                <td class="py-3 px-4 text-right space-x-3">
                    @if($link->student_id)
                        <a href="{{ route('admin.learning.parental.student', $link->student_id) }}"
                           class="text-purple-600 hover:text-purple-800 font-medium text-xs">View Progress</a>
                    @endif
                    @if($link->status === 'pending')
                        <form action="{{ route('admin.learning.parental.approve', $link) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-green-600 hover:text-green-800 font-medium text-xs">Approve</button>
                        </form>
                    @endif
                    <form action="{{ route('admin.learning.parental.destroy', $link) }}" method="POST"
                          class="inline" onsubmit="return confirm('Remove this parent-student link?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-xs">Remove</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-10 text-center text-gray-400">No parent-student links found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $links->links() }}</div>
</div>
@endsection
