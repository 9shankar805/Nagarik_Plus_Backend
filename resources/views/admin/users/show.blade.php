@extends('admin.layouts.app')

@section('title', $user->name)
@section('subtitle', 'User profile and activity')

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Users</a>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    {{-- Profile Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="text-center mb-6">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <span class="text-blue-700 font-bold text-2xl">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            </div>
            <h2 class="text-lg font-bold text-gray-800">{{ $user->name }}</h2>
            <p class="text-sm text-gray-500">{{ $user->email }}</p>
        </div>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Phone</span>
                <span class="font-medium text-gray-800">{{ $user->phone ?? '—' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Role</span>
                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">{{ ucfirst($user->role ?? 'user') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Status</span>
                @if($user->is_active)
                    <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Active</span>
                @else
                    <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">Inactive</span>
                @endif
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Joined</span>
                <span class="font-medium text-gray-800">{{ $user->created_at->format('d M Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Last Active</span>
                <span class="font-medium text-gray-800">{{ $user->last_active_at ? $user->last_active_at->diffForHumans() : '—' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Language</span>
                <span class="font-medium text-gray-800">{{ strtoupper($user->preferred_language ?? 'en') }}</span>
            </div>
        </div>
    </div>

    {{-- KYC Verification Dashboard --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
        <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">KYC Verification</h3>
        
        <div class="mb-4">
            <span class="text-gray-500 text-sm">Status:</span>
            @if($user->kyc_status === 'verified')
                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Verified</span>
            @elseif($user->kyc_status === 'rejected')
                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Rejected</span>
            @elseif($user->kyc_status === 'pending')
                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Pending Review</span>
            @else
                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Unverified</span>
            @endif
        </div>

        @if($user->kyc_status !== 'unverified')
            <div class="space-y-4">
                <div>
                    <span class="block text-gray-500 text-sm">Citizenship Number</span>
                    <span class="font-medium text-gray-800">{{ $user->citizenship_number }}</span>
                </div>
                
                <div>
                    <span class="block text-gray-500 text-sm mb-2">Front Image</span>
                    @if($user->citizenship_front_image)
                        <a href="{{ Storage::url($user->citizenship_front_image) }}" target="_blank">
                            <img src="{{ Storage::url($user->citizenship_front_image) }}" alt="Citizenship Front" class="w-full h-32 object-cover rounded border">
                        </a>
                    @else
                        <span class="text-sm text-gray-400">Not provided</span>
                    @endif
                </div>

                <div>
                    <span class="block text-gray-500 text-sm mb-2">Back Image</span>
                    @if($user->citizenship_back_image)
                        <a href="{{ Storage::url($user->citizenship_back_image) }}" target="_blank">
                            <img src="{{ Storage::url($user->citizenship_back_image) }}" alt="Citizenship Back" class="w-full h-32 object-cover rounded border">
                        </a>
                    @else
                        <span class="text-sm text-gray-400">Not provided</span>
                    @endif
                </div>
                
                @if($user->kyc_status === 'pending')
                    <div class="pt-4 border-t border-gray-100 mt-4">
                        <form action="{{ route('admin.users.kyc_review', $user) }}" method="POST">
                            @csrf
                            <div class="mb-3 flex items-center gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="status" value="verified" class="w-4 h-4 text-green-600" onclick="document.getElementById('kyc_rejection_div').classList.add('hidden')" required>
                                    <span>Approve KYC</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer mt-2">
                                    <input type="radio" name="status" value="rejected" class="w-4 h-4 text-red-600" onclick="document.getElementById('kyc_rejection_div').classList.remove('hidden')" required>
                                    <span>Reject KYC</span>
                                </label>
                            </div>
                            
                            <div id="kyc_rejection_div" class="hidden mb-3">
                                <textarea name="rejection_reason" class="w-full border-gray-300 rounded p-2 text-sm" rows="2" placeholder="Reason for rejection (if rejected)"></textarea>
                            </div>
                            
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded text-sm transition">Submit Decision</button>
                        </form>
                    </div>
                @endif
                
                @if($user->kyc_status === 'rejected')
                    <div class="bg-red-50 p-3 rounded text-sm mt-2 border border-red-100">
                        <span class="font-bold text-red-700 block">Rejection Reason:</span>
                        <p class="text-red-600">{{ $user->kyc_rejection_reason }}</p>
                    </div>
                @endif
                
                @if($user->kyc_status === 'verified')
                    <div class="text-sm text-gray-500 mt-2">
                        Verified at: {{ $user->kyc_verified_at ? \Carbon\Carbon::parse($user->kyc_verified_at)->format('d M Y H:i') : 'Unknown' }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- Documents & Reminders --}}
    <div class="xl:col-span-2 space-y-6">
        {{-- Documents --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Documents ({{ $user->documents->count() }})</h3>
            </div>
            @if($user->documents->isEmpty())
                <p class="px-6 py-8 text-center text-gray-400 text-sm">No documents.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($user->documents as $doc)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-3 font-medium text-gray-800">{{ $doc->title }}</td>
                                    <td class="px-6 py-3 text-gray-600">{{ ucfirst($doc->type) }}</td>
                                    <td class="px-6 py-3">
                                        <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full
                                            {{ $doc->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ ucfirst($doc->status ?? 'active') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-gray-500">
                                        {{ $doc->expiry_date ? $doc->expiry_date->format('d M Y') : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Reminders --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Reminders ({{ $user->reminders->count() }})</h3>
            </div>
            @if($user->reminders->isEmpty())
                <p class="px-6 py-8 text-center text-gray-400 text-sm">No reminders.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Enabled</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($user->reminders as $reminder)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-3 font-medium text-gray-800">{{ $reminder->title ?? '—' }}</td>
                                    <td class="px-6 py-3 text-gray-500">
                                        {{ isset($reminder->remind_at) ? \Carbon\Carbon::parse($reminder->remind_at)->format('d M Y') : '—' }}
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        @if($reminder->is_enabled ?? true)
                                            <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Yes</span>
                                        @else
                                            <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600">No</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
