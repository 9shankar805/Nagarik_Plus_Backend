@extends('admin.layouts.app')

@section('title', 'Registrations')
@section('subtitle', $competition->title)

@section('content')
<div class="mb-6 flex items-start justify-between">
    <div>
        <a href="{{ route('admin.learning.competitions.show', $competition) }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Overview</a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">Registrations — {{ $competition->title }}</h2>
        <p class="text-sm text-gray-500 mt-1">
            {{ $registrations->total() }} total registrations
            @if($competition->max_participants)
                · Max {{ $competition->max_participants }} seats
            @endif
        </p>
    </div>
    <div class="mt-6">
        <span class="px-3 py-1.5 text-xs font-semibold rounded-full
            {{ ['draft'=>'bg-gray-100 text-gray-600','open'=>'bg-blue-100 text-blue-700','ongoing'=>'bg-green-100 text-green-700','completed'=>'bg-purple-100 text-purple-700','cancelled'=>'bg-red-100 text-red-600'][$competition->status] ?? 'bg-gray-100 text-gray-600' }}">
            {{ ucfirst($competition->status) }}
        </span>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">#</th>
                <th class="py-3 px-4">Participant</th>
                <th class="py-3 px-4">Phone</th>
                <th class="py-3 px-4 text-center">Payment</th>
                <th class="py-3 px-4">Method</th>
                <th class="py-3 px-4">Reference</th>
                <th class="py-3 px-4">Registered</th>
                <th class="py-3 px-4">Paid At</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($registrations as $i => $reg)
            <tr class="hover:bg-gray-50">
                <td class="py-3 px-4 text-gray-400 text-xs">{{ $registrations->firstItem() + $i }}</td>
                <td class="py-3 px-4">
                    <div class="flex items-center gap-2">
                        @if($reg->user?->avatar)
                            <img src="{{ $reg->user->avatar }}" class="w-7 h-7 rounded-full object-cover">
                        @else
                            <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                {{ strtoupper(substr($reg->user?->name ?? 'U', 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <div class="font-medium text-gray-900">{{ $reg->user?->name ?? 'Deleted User' }}</div>
                            <div class="text-xs text-gray-400">{{ $reg->user?->email }}</div>
                        </div>
                    </div>
                </td>
                <td class="py-3 px-4 text-xs text-gray-600">{{ $reg->user?->phone ?? '—' }}</td>
                <td class="py-3 px-4 text-center">
                    @php $ps = $reg->payment_status; @endphp
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                        {{ $ps == 'paid' ? 'bg-green-100 text-green-800' : ($ps == 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-600') }}">
                        {{ ucfirst($ps) }}
                    </span>
                </td>
                <td class="py-3 px-4 text-xs text-gray-600">{{ $reg->payment_method ? ucfirst($reg->payment_method) : '—' }}</td>
                <td class="py-3 px-4 text-xs font-mono text-gray-500">{{ $reg->payment_reference ?? '—' }}</td>
                <td class="py-3 px-4 text-xs text-gray-500">{{ $reg->registered_at?->format('d M Y, h:i A') }}</td>
                <td class="py-3 px-4 text-xs text-gray-500">{{ $reg->paid_at?->format('d M Y, h:i A') ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="py-10 text-center text-gray-400">No registrations yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $registrations->links() }}</div>
</div>
@endsection
