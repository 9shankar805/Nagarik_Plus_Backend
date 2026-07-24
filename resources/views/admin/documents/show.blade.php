@extends('admin.layouts.app')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.documents.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Documents</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-3xl">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $document->title }}</h1>
            <p class="text-sm text-gray-500">Document ID: #{{ $document->id }}</p>
        </div>
        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
            {{ ucfirst($document->type ?? 'General') }}
        </span>
    </div>

    <div class="space-y-4 text-sm border-t border-gray-100 pt-4">
        <div class="flex justify-between py-2 border-b border-gray-50">
            <span class="text-gray-500 font-medium">Owner Citizen</span>
            <span class="text-gray-900 font-semibold">{{ $document->user->name ?? 'User #' . $document->user_id }}</span>
        </div>
        <div class="flex justify-between py-2 border-b border-gray-50">
            <span class="text-gray-500 font-medium">Document Number</span>
            <span class="text-gray-900 font-mono">{{ $document->document_number ?? '-' }}</span>
        </div>
        <div class="flex justify-between py-2 border-b border-gray-50">
            <span class="text-gray-500 font-medium">Issued Date</span>
            <span class="text-gray-900">{{ $document->issued_date ?? '-' }}</span>
        </div>
        <div class="flex justify-between py-2 border-b border-gray-50">
            <span class="text-gray-500 font-medium">Expiry Date</span>
            <span class="text-gray-900">{{ $document->expiry_date ?? '-' }}</span>
        </div>
        <div class="flex justify-between py-2 border-b border-gray-50">
            <span class="text-gray-500 font-medium">Verification Status</span>
            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                Verified
            </span>
        </div>
    </div>
</div>
@endsection
