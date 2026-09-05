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
                                        'marriage_certificate' => 'marriagecertificate.png',
                                        'migration_certificate' => 'migrationcertificate.png',
                                        'death_certificate' => 'deathcertificate.png',
                                        'vehicle_bluebook' => 'SSF1752476396810.png',
                                        'insurance' => 'cims1752476325868.png',
                                        'medical' => 'pcr1752476863055.png',
                                        'property' => 'dolma1752476593369.png',
                                        'academic' => 'slc1631011325238.jpg',
                                        'nea_bill' => 'nea1752476414169.png',
                                        'gunaso' => 'gunaso1752476491251.png',
                                        'press_pass' => 'patrakar1752477622244.png',
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
        <!-- Document Verification -->
        <div class="bg-gradient-to-br from-white to-blue-50 rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
            <div class="px-7 py-5 border-b border-blue-100 flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Government Document Verify</h2>
                    <p class="text-xs text-gray-500">NID · Licence · PAN · Citizenship</p>
                </div>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 gap-2 mb-4">
                    @foreach([
                        ['NID', 'blue',   'M15 9a2 2 0 10-4 0v5a2 2 0 01-2 2h6m-6-4h4m8 0a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['Licence', 'green', 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z'],
                        ['PAN', 'orange', 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['Citizenship', 'purple', 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2'],
                    ] as [$label, $color, $path])
                    <div class="flex items-center gap-2 bg-{{ $color }}-50 border border-{{ $color }}-100 rounded-xl px-3 py-2">
                        <svg class="w-4 h-4 text-{{ $color }}-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/>
                        </svg>
                        <span class="text-xs font-semibold text-{{ $color }}-800">{{ $label }}</span>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('user.nid-download') }}"
                   class="block w-full text-center bg-[#4A5D4A] hover:bg-[#3A4D3A] text-white text-xs font-semibold py-2.5 rounded-xl transition-all shadow-sm">
                    Go to Verification →
                </a>
            </div>
        </div>
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
