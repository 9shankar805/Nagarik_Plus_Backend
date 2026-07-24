@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Overview of your Nagarik+ platform')

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <div class="bg-[#E1E8E1] rounded-2xl p-6 shadow-sm">
        <p class="text-3xl font-bold text-[#334033] mb-1">{{ number_format($stats['total_users']) }}</p>
        <p class="text-sm text-[#4A5D4A]">Registered Citizens</p>
    </div>

    <div class="bg-[#E1E8E1] rounded-2xl p-6 shadow-sm">
        <p class="text-3xl font-bold text-[#334033] mb-1">{{ number_format($stats['total_documents']) }}</p>
        <p class="text-sm text-[#4A5D4A]">Digital Locker Docs</p>
    </div>

    <div class="bg-[#E8DFE1] rounded-2xl p-6 shadow-sm">
        <p class="text-3xl font-bold text-[#403333] mb-1">{{ number_format($stats['total_advisors']) }}</p>
        <p class="text-sm text-[#5D4A4A]">Nagarik Advisors</p>
    </div>

    <div class="bg-[#E8DFE1] rounded-2xl p-6 shadow-sm">
        <p class="text-3xl font-bold text-[#403333] mb-1">{{ number_format($stats['total_reminders']) }}</p>
        <p class="text-sm text-[#5D4A4A]">Active Reminders</p>
    </div>

    <div class="bg-[#E1E8E1] rounded-2xl p-6 shadow-sm">
        <p class="text-3xl font-bold text-[#334033] mb-1">{{ number_format($stats['total_hospitals']) }}</p>
        <p class="text-sm text-[#4A5D4A]">Emergency Hospitals</p>
    </div>

    <div class="bg-[#E1E8E1] rounded-2xl p-6 shadow-sm">
        <p class="text-3xl font-bold text-[#334033] mb-1">{{ number_format($stats['published_news']) }}</p>
        <p class="text-sm text-[#4A5D4A]">Published News</p>
    </div>

    <div class="bg-[#E8DFE1] rounded-2xl p-6 shadow-sm">
        <p class="text-3xl font-bold text-[#403333] mb-1">{{ number_format($stats['total_offices']) }}</p>
        <p class="text-sm text-[#5D4A4A]">Government Offices</p>
    </div>

    <div class="bg-[#E8DFE1] rounded-2xl p-6 shadow-sm">
        <p class="text-3xl font-bold text-[#403333] mb-1">{{ number_format($stats['total_tokens']) }}</p>
        <p class="text-sm text-[#5D4A4A]">FCM Device Tokens</p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    {{-- Recent Users --}}
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Recent Users</h2>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-[#4A5D4A] hover:text-[#334033] font-medium">View all →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentUsers as $user)
                        <tr class="hover:bg-[#E1E8E1] transition-colors">
                            <td class="px-6 py-3 font-medium text-gray-800">{{ $user->name }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $user->phone ?? '—' }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">No users yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="space-y-6">
        {{-- Recent News --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Recent News</h2>
                <a href="{{ route('admin.news.index') }}" class="text-sm text-[#4A5D4A] hover:text-[#334033] font-medium">View all →</a>
            </div>
            <div class="p-6">
                @forelse($recentNews as $article)
                    <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-[#E1E8E1] transition mb-3">
                        <div class="w-10 h-10 bg-[#E8DFE1] rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-[#5D4A4A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-800 text-sm truncate">{{ $article->title }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $article->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400 text-sm">No news yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection
