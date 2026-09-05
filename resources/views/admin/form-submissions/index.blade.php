@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Digital Form Submissions</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">ID</th>
                <th class="py-3 px-4">User</th>
                <th class="py-3 px-4">Form Type</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4">Submitted At</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($submissions as $sub)
            <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4 text-gray-500">#{{ $sub->id }}</td>
                <td class="py-3 px-4 font-semibold text-gray-900">{{ $sub->user->name }}</td>
                <td class="py-3 px-4 text-gray-600">
                    @if($sub->vitalEvent)
                        Vital Event: {{ $sub->vitalEvent->title_en }}
                    @elseif($sub->documentTemplate)
                        Template: {{ $sub->documentTemplate->title }}
                    @else
                        Unknown
                    @endif
                </td>
                <td class="py-3 px-4">
                    @if($sub->status === 'approved')
                        <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">Approved</span>
                    @elseif($sub->status === 'rejected')
                        <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">Rejected</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">Pending</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-gray-500">{{ $sub->created_at->format('M d, Y H:i') }}</td>
                <td class="py-3 px-4 text-right space-x-2">
                    <a href="{{ route('admin.form-submissions.show', $sub) }}" class="text-blue-600 hover:text-blue-800 font-medium">Review</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-8 text-center text-gray-500">No form submissions found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">
        {{ $submissions->links() }}
    </div>
</div>
@endsection
