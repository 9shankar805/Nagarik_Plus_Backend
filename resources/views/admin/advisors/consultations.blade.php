@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <a href="{{ route('admin.advisors.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Advisors</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Advisor Consultation Bookings</h1>
        <p class="text-sm text-gray-600">Track all chat and call consultation appointments booked by citizens.</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Citizen</th>
                <th class="py-3 px-4">Advisor</th>
                <th class="py-3 px-4">Type</th>
                <th class="py-3 px-4">Fee Amount</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4">Booked At</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($consultations as $item)
            <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4 font-semibold text-gray-900">
                    {{ $item->user->name ?? 'User #' . $item->user_id }}
                </td>
                <td class="py-3 px-4 text-gray-800 font-medium">
                    {{ $item->advisor->name ?? 'Advisor #' . $item->advisor_id }}
                </td>
                <td class="py-3 px-4">
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                        {{ strtoupper($item->type ?? 'CHAT') }}
                    </span>
                </td>
                <td class="py-3 px-4 font-bold text-gray-900">
                    Rs. {{ number_format($item->amount ?? 250) }}
                </td>
                <td class="py-3 px-4">
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                        {{ ucfirst($item->status ?? 'Confirmed') }}
                    </span>
                </td>
                <td class="py-3 px-4 text-gray-500 text-xs">
                    {{ $item->created_at ? $item->created_at->format('M d, Y H:i') : 'Recently' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-8 text-center text-gray-500">No consultation bookings recorded yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">
        {{ $consultations->links() }}
    </div>
</div>
@endsection
