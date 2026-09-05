@extends('admin.layouts.app')

@section('title', 'Transactions')
@section('subtitle', 'View payment gateway transactions')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-1">Total Volume</h3>
        <p class="text-2xl font-bold text-gray-900">Rs. {{ number_format($stats['total_volume'], 2) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-1">Today's Volume</h3>
        <p class="text-2xl font-bold text-green-600">Rs. {{ number_format($stats['today_volume'], 2) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-1">Pending Transactions</h3>
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_count'] }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-4 font-medium">Txn Code</th>
                    <th class="px-6 py-4 font-medium">User</th>
                    <th class="px-6 py-4 font-medium">Gateway</th>
                    <th class="px-6 py-4 font-medium">Amount</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                    <th class="px-6 py-4 font-medium">Date</th>
                    <th class="px-6 py-4 font-medium text-right">View</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transactions as $txn)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 text-gray-900 font-mono text-xs">{{ $txn->transaction_code ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $txn->user->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4">
                            @if($txn->payment_method === 'esewa')
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded font-bold">eSewa</span>
                            @elseif($txn->payment_method === 'khalti')
                                <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded font-bold">Khalti</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded font-bold">{{ strtoupper($txn->payment_method ?? 'N/A') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-900 font-medium">Rs. {{ number_format($txn->amount, 2) }}</td>
                        <td class="px-6 py-4">
                            @if($txn->status === 'completed')
                                <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Completed</span>
                            @elseif($txn->status === 'pending')
                                <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">Pending</span>
                            @elseif($txn->status === 'failed')
                                <span class="px-2.5 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">Failed</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs">{{ $txn->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.transactions.show', $txn) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Details</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                            No transactions found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
    @endif
</div>

@endsection
