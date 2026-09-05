@extends('admin.layouts.app')

@section('title', 'Subscribers — ' . $subscription->name)
@section('subtitle', 'All users subscribed to this package')

@section('content')
<div class="mb-6 flex justify-between items-start">
    <div>
        <a href="{{ route('admin.learning.subscriptions.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Packages</a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">{{ $subscription->name }}</h2>
        <p class="text-sm text-gray-500">Rs {{ number_format($subscription->price) }} &bull; {{ $subscription->duration_days }} days</p>
    </div>
    <a href="{{ route('admin.learning.subscriptions.edit', $subscription) }}"
       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">Edit Package</a>
</div>

{{-- Stats --}}
<div class="grid grid-cols-3 gap-5 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-[#4A5D4A]">{{ $activeCount }}</div>
        <div class="text-sm text-gray-500 mt-1">Active Subscribers</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-gray-800">{{ $subscriptions->total() }}</div>
        <div class="text-sm text-gray-500 mt-1">Total Subscribers</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
        <div class="text-3xl font-bold text-green-600">Rs {{ number_format($totalRevenue) }}</div>
        <div class="text-sm text-gray-500 mt-1">Estimated Revenue</div>
    </div>
</div>

{{-- Subscriber list --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">User</th>
                <th class="py-3 px-4">Email</th>
                <th class="py-3 px-4">Phone</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4">Expires At</th>
                <th class="py-3 px-4">Subscribed On</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($subscriptions as $sub)
            <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4 font-medium text-gray-900">{{ $sub->user?->name ?? '—' }}</td>
                <td class="py-3 px-4 text-gray-600">{{ $sub->user?->email ?? '—' }}</td>
                <td class="py-3 px-4 text-gray-600">{{ $sub->user?->phone ?? '—' }}</td>
                <td class="py-3 px-4 text-center">
                    @if($sub->status === 'active')
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    @elseif($sub->status === 'expired')
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Expired</span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-500">{{ ucfirst($sub->status) }}</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-gray-600 text-xs">
                    {{ $sub->expires_at ? $sub->expires_at->format('d M Y') : '—' }}
                </td>
                <td class="py-3 px-4 text-gray-400 text-xs">{{ $sub->created_at->format('d M Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-10 text-center text-gray-400">No subscribers yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $subscriptions->links() }}</div>
</div>
@endsection
