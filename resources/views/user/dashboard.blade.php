@extends('layouts.user')

@section('title', 'Dashboard')
@section('subtitle', 'Welcome back, ' . auth()->user()->name . '!')

@section('content')
<!-- Stat Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-white to-emerald-50 rounded-3xl p-6 border border-emerald-100 shadow-sm card-hover">
        <div class="flex items-center justify-between mb-5">
            <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-full">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
                +12%
            </span>
        </div>
        <p class="text-3xl font-bold text-gray-900 mb-1">{{ auth()->user()->documents->count() }}</p>
        <p class="text-sm text-gray-600 font-medium">Total Documents</p>
    </div>

    <div class="bg-gradient-to-br from-white to-blue-50 rounded-3xl p-6 border border-blue-100 shadow-sm card-hover">
        <div class="flex items-center justify-between mb-5">
            <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-700 bg-blue-100 px-2.5 py-1 rounded-full">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
                -5%
            </span>
        </div>
        <p class="text-3xl font-bold text-gray-900 mb-1">{{ auth()->user()->documents()->expiringSoon(30)->count() }}</p>
        <p class="text-sm text-gray-600 font-medium">Expiring Soon</p>
    </div>

    <div class="bg-gradient-to-br from-white to-amber-50 rounded-3xl p-6 border border-amber-100 shadow-sm card-hover">
        <div class="flex items-center justify-between mb-5">
            <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center">
                <svg class="w-6 h-6 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 bg-amber-100 px-2.5 py-1 rounded-full">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
                +3%
            </span>
        </div>
        <p class="text-3xl font-bold text-gray-900 mb-1">{{ auth()->user()->reminders()->where('is_enabled', true)->count() }}</p>
        <p class="text-sm text-gray-600 font-medium">Active Reminders</p>
    </div>

    <div class="bg-gradient-to-br from-white to-purple-50 rounded-3xl p-6 border border-purple-100 shadow-sm card-hover">
        <div class="flex items-center justify-between mb-5">
            <div class="w-12 h-12 bg-purple-100 rounded-2xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="inline-flex items-center gap-1 text-xs font-semibold text-purple-700 bg-purple-100 px-2.5 py-1 rounded-full">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
                +12%
            </span>
        </div>
        <p class="text-3xl font-bold text-gray-900 mb-1">{{ auth()->user()->created_at->format('M Y') }}</p>
        <p class="text-sm text-gray-600 font-medium">Member Since</p>
    </div>
</div>

<!-- Main Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Digital Locker Documents -->
    <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-7 py-5 border-b border-gray-100 bg-[#E1E8E1]">
            <div>
                <h2 class="text-xl font-bold text-[#4A5D4A] flex items-center gap-2">
                    <svg class="w-6 h-6 text-[#4A5D4A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Digital Locker Vault
                </h2>
                <p class="text-xs text-[#4A5D4A] font-medium mt-0.5">Encrypted document storage & activity management</p>
            </div>
            <a href="{{ route('user.documents') }}" class="bg-[#4A5D4A] text-white hover:bg-[#3A4D3A] px-4 py-2 rounded-xl font-medium text-xs transition-all shadow-sm">
                Open Digital Locker →
            </a>
        </div>
        <div class="p-6">
            @if(auth()->user()->documents->count() > 0)
                <div class="space-y-4">
                    @foreach(auth()->user()->documents()->latest()->take(5)->get() as $doc)
                        @php
                            $daysLeft = $doc->daysUntilExpiry();
                            $isExpired = $doc->isExpired();
                        @endphp
                        <a href="{{ route('user.documents') }}" class="flex items-center gap-4 p-4 rounded-2xl hover:bg-[#E1E8E1]/50 border border-gray-50 hover:border-gray-200 transition-all group">
                            <div class="w-12 h-12 bg-[#E1E8E1] rounded-2xl flex items-center justify-center flex-shrink-0 overflow-hidden shadow-sm">
                                @php
                                    $typeImages = [
                                        'national_id' => 'nid1752476653129.png',
                                        'passport' => 'passport1752476337775.png',
                                        'driving_license' => 'license1752476621950.png',
                                        'pan' => 'pan.png',
                                        'citizenship' => 'cit1759940267390.png',
                                        'voter_id' => 'voterid.png',
                                        'birth_certificate' => 'birthcertificate.png',
                                        'vehicle_bluebook' => 'SSF1752476396810.png',
                                        'insurance' => 'cims1752476325868.png',
                                        'medical' => 'pcr1752476863055.png',
                                        'property' => 'dolma1752476593369.png',
                                        'academic' => 'slc1631011325238.jpg',
                                        'other' => 'unnamed.webp',
                                    ];
                                @endphp
                                <img src="{{ asset('assets/images/' . ($typeImages[$doc->type] ?? 'unnamed.webp')) }}" alt="Document" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="font-bold text-gray-900 truncate text-sm">{{ $doc->title }}</p>
                                    <span class="text-[10px] bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded font-mono">Encrypted</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5 flex items-center gap-3">
                                    <span>Added {{ $doc->created_at->format('d M Y') }}</span>
                                    @if($doc->expiry_date)
                                    <span>•</span>
                                    <span class="{{ $isExpired ? 'text-red-600 font-semibold' : ($daysLeft <= 30 ? 'text-amber-600 font-semibold' : 'text-emerald-700') }}">
                                        {{ $isExpired ? 'Expired' : ($daysLeft . ' days left') }}
                                    </span>
                                    @endif
                                </p>
                            </div>
                            <div class="w-2.5 h-2.5 rounded-full {{ $isExpired ? 'bg-red-500' : ($daysLeft !== null && $daysLeft <= 30 ? 'bg-amber-500' : 'bg-emerald-500') }}"></div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-[#E1E8E1] rounded-3xl flex items-center justify-center mx-auto mb-4 text-[#4A5D4A]">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-gray-600 font-semibold">No documents stored in Digital Locker</p>
                    <p class="text-xs text-gray-500 mt-1">Upload and protect your Citizenship, Passport, Licenses, and Certificates securely.</p>
                    <a href="{{ route('user.documents') }}" class="inline-block mt-4 bg-[#4A5D4A] text-white px-5 py-2.5 rounded-xl text-xs font-semibold hover:bg-[#3A4D3A] transition-all shadow-sm">Upload to Digital Locker →</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column -->
    <div class="space-y-6">
        <!-- Upcoming Reminders -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-7 py-5 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900">Upcoming Reminders</h2>
            </div>
            <div class="p-6">
                @if(auth()->user()->reminders()->where('is_enabled', true)->count() > 0)
                    <div class="space-y-4">
                        @foreach(auth()->user()->reminders()->where('is_enabled', true)->latest()->take(3)->get() as $reminder)
                            <div class="p-4 rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-900 truncate text-sm">{{ $reminder->title }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $reminder->due_date?->format('d M Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <div class="w-16 h-16 bg-gray-100 rounded-3xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium text-sm">No reminders yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-7 py-5 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900">Recent Activity</h2>
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                </div>
            </div>
            <div class="p-6">
                @if(auth()->user()->activityLogs()->count() > 0)
                    <div class="space-y-4">
                        @foreach(auth()->user()->activityLogs()->latest()->take(4)->get() as $log)
                            <div class="flex items-start gap-3 p-4 rounded-2xl hover:bg-emerald-50 transition-all">
                                <div class="w-2 h-2 bg-emerald-600 rounded-full mt-2.5"></div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $log->description }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $log->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <div class="w-16 h-16 bg-gray-100 rounded-3xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium text-sm">No activity yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
