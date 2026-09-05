@extends('admin.layouts.app')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.form-submissions.index') }}" class="text-blue-600 hover:underline">&larr; Back to Submissions</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <h2 class="text-lg font-bold text-gray-900">Submission Details (#{{ $formSubmission->id }})</h2>
        <div>
            @if($formSubmission->status === 'approved')
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
            @elseif($formSubmission->status === 'rejected')
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
            @else
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
            @endif
        </div>
    </div>
    <div class="p-6 grid grid-cols-2 gap-6">
        <div>
            <h3 class="font-semibold text-gray-700 mb-2">User Information</h3>
            <p><strong>Name:</strong> {{ $formSubmission->user->name }}</p>
            <p><strong>Email:</strong> {{ $formSubmission->user->email }}</p>
            <p><strong>Phone:</strong> {{ $formSubmission->user->phone ?? 'N/A' }}</p>
        </div>
        <div>
            <h3 class="font-semibold text-gray-700 mb-2">Form Information</h3>
            @if($formSubmission->vitalEvent)
                <p><strong>Vital Event:</strong> {{ $formSubmission->vitalEvent->title_en }}</p>
            @endif
            @if($formSubmission->documentTemplate)
                <p><strong>Template:</strong> {{ $formSubmission->documentTemplate->title }}</p>
            @endif
            <p><strong>Submitted:</strong> {{ $formSubmission->created_at->format('M d, Y H:i:s') }}</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
        <h2 class="text-lg font-bold text-gray-900">Submitted Data</h2>
    </div>
    <div class="p-6">
        <table class="w-full text-left text-sm border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 border">Field Name</th>
                    <th class="p-3 border">Submitted Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($formSubmission->form_data as $key => $value)
                <tr>
                    <td class="p-3 border font-medium text-gray-700">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                    <td class="p-3 border">{{ is_array($value) ? json_encode($value) : $value }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
        <h2 class="text-lg font-bold text-gray-900">Review Actions</h2>
    </div>
    <div class="p-6">
        <form action="{{ route('admin.form-submissions.update', $formSubmission) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Decision</label>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="pending" {{ $formSubmission->status === 'pending' ? 'checked' : '' }} class="w-4 h-4 text-blue-600" onclick="document.getElementById('rejection_div').classList.add('hidden')">
                        <span>Pending</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="approved" {{ $formSubmission->status === 'approved' ? 'checked' : '' }} class="w-4 h-4 text-green-600" onclick="document.getElementById('rejection_div').classList.add('hidden')">
                        <span>Approve</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="rejected" {{ $formSubmission->status === 'rejected' ? 'checked' : '' }} class="w-4 h-4 text-red-600" onclick="document.getElementById('rejection_div').classList.remove('hidden')">
                        <span>Reject</span>
                    </label>
                </div>
            </div>

            <div id="rejection_div" class="mb-6 {{ $formSubmission->status === 'rejected' ? '' : 'hidden' }}">
                <label class="block text-gray-700 font-medium mb-2">Rejection Reason</label>
                <textarea name="rejection_reason" class="w-full border-gray-300 rounded-lg p-3" rows="3" placeholder="Enter reason for rejection...">{{ old('rejection_reason', $formSubmission->rejection_reason) }}</textarea>
            </div>
            
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">Save Review</button>
        </form>
    </div>
</div>
@endsection
