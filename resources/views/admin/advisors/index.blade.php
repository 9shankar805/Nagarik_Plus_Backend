@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Nagarik Advisors</h1>
        <p class="text-sm text-gray-600">Manage legal, tax, consular, and property advisors for citizen consultations.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.advisors.consultations') }}" class="px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
            View Bookings
        </a>
        <a href="{{ route('admin.advisors.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm flex items-center gap-2">
            <span>+ Add Advisor</span>
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Advisor Name</th>
                <th class="py-3 px-4">Category</th>
                <th class="py-3 px-4">Fees (Chat / Call)</th>
                <th class="py-3 px-4">Rating</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($advisors as $advisor)
            <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4 font-semibold text-gray-900">
                    <div>{{ $advisor->name }}</div>
                    <div class="text-xs text-gray-500 font-normal">{{ $advisor->title_en }}</div>
                </td>
                <td class="py-3 px-4">
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-purple-50 text-purple-700 border border-purple-200">
                        {{ ucfirst($advisor->category) }}
                    </span>
                </td>
                <td class="py-3 px-4 text-gray-700 font-medium">
                    Rs. {{ $advisor->consultation_fee_chat }} / Rs. {{ $advisor->consultation_fee_call }}
                </td>
                <td class="py-3 px-4 font-bold text-amber-600">
                    ★ {{ $advisor->rating ?? '4.9' }}
                </td>
                <td class="py-3 px-4">
                    @if($advisor->is_online)
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Online</span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Offline</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-right space-x-2">
                    <a href="{{ route('admin.advisors.edit', $advisor) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                    <form action="{{ route('admin.advisors.destroy', $advisor) }}" method="POST" class="inline" onsubmit="return confirm('Delete advisor?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-8 text-center text-gray-500">No advisors registered. Click "+ Add Advisor" to register one.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">
        {{ $advisors->links() }}
    </div>
</div>
@endsection
