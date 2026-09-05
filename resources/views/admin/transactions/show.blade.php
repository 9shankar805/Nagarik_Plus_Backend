@extends('admin.layouts.app')

@section('title', 'Transaction Details')
@section('subtitle', $transaction->transaction_code ?? 'Pending Transaction')

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.transactions.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Transactions</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-gray-900 mb-4 pb-2 border-b">Payment Information</h3>
        
        <div class="space-y-4 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Status</span>
                @if($transaction->status === 'completed')
                    <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Completed</span>
                @elseif($transaction->status === 'pending')
                    <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">Pending</span>
                @elseif($transaction->status === 'failed')
                    <span class="px-2.5 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">Failed</span>
                @endif
            </div>
            
            <div class="flex justify-between">
                <span class="text-gray-500">Amount</span>
                <span class="font-bold text-gray-900">Rs. {{ number_format($transaction->amount, 2) }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Gateway</span>
                <span class="font-medium text-gray-800 uppercase">{{ $transaction->payment_method }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Transaction Code</span>
                <span class="font-mono text-gray-800">{{ $transaction->transaction_code ?? 'N/A' }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Date</span>
                <span class="font-medium text-gray-800">{{ $transaction->created_at->format('d M Y, h:i A') }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-gray-900 mb-4 pb-2 border-b">Payee Information</h3>
        
        <div class="space-y-4 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">User</span>
                <a href="{{ route('admin.users.show', $transaction->user_id) }}" class="font-medium text-blue-600 hover:underline">{{ $transaction->user->name ?? 'Unknown' }}</a>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Purpose (Type)</span>
                <span class="font-medium text-gray-800">{{ class_basename($transaction->payable_type) }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Reference ID</span>
                <span class="font-medium text-gray-800">#{{ $transaction->payable_id }}</span>
            </div>
        </div>
    </div>
    
    @if($transaction->gateway_response)
        <div class="lg:col-span-2 bg-gray-50 rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-bold text-gray-900 mb-4">Raw Gateway Response</h3>
            <pre class="text-xs bg-gray-800 text-green-400 p-4 rounded-lg overflow-x-auto"><code>{{ json_encode($transaction->gateway_response, JSON_PRETTY_PRINT) }}</code></pre>
        </div>
    @endif
</div>

@endsection
