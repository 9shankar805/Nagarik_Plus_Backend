<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('admin.app_name') }} — @yield('title', __('admin.dashboard'))</title>
    <link rel="icon" href="/icon.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Noto Sans Devanagari', 'Inter', sans-serif;
            background: linear-gradient(135deg, #D8E0D8 0%, #E1E8E1 100%);
        }
        [x-cloak] { display: none !important; }
    </style>
    <x-firebase-init />
</head>
<body class="min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="fixed top-0 left-0 h-full w-64 flex flex-col z-30 bg-[#4A5D4A]">
        <div class="flex items-center gap-3 px-6 py-5 border-b border-[#3F523F]">
            <img src="/icon.png" alt="Nagarik+" class="w-10 h-10 rounded-xl object-cover">
            <span class="text-white font-bold text-xl">{{ __('admin.app_name') }}</span>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.dashboard') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                {{ __('admin.dashboard') }}
            </a>

            @if(!auth()->user()->isLearningAdmin())
            <a href="{{ route('admin.users.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.users.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ __('admin.users') }}
            </a>

            <a href="{{ route('admin.users.index', ['kyc_status' => 'pending']) }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->fullUrlIs(route('admin.users.index', ['kyc_status' => 'pending'])) ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Pending KYC
            </a>

            <a href="{{ route('admin.form-submissions.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.form-submissions.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                Digital Forms
            </a>

            <a href="{{ route('admin.grievances.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.grievances.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                Hello Sarkar (Grievances)
            </a>

            <a href="{{ route('admin.transactions.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.transactions.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Payments & Fees
            </a>

            <a href="{{ route('admin.audit-logs.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.audit-logs.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Audit Logs
            </a>

            <a href="{{ route('admin.documents.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.documents.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                {{ __('admin.documents') }}
            </a>

            <a href="{{ route('admin.document-templates.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.document-templates.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12V7a3 3 0 013-3h.382a1 1 0 01.894.553l1 2a1 1 0 00.894.553H15a3 3 0 013 3v1M9 12l-1.724 6.034A2 2 0 009.265 21H18a2 2 0 002-2v-5a2 2 0 00-2-2H9z"/>
                </svg>
                Templates
            </a>

            <a href="{{ route('admin.news.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.news.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                {{ __('admin.news') }}
                @php($_pendingNews = Cache::store('array')->remember('_admin_pending_news', 15, fn () => \App\Models\News::pending()->count()))
                @if($_pendingNews > 0)
                    <span class="ml-auto inline-flex items-center justify-center min-w-[22px] h-5 px-1.5 text-[11px] font-bold leading-none rounded-full bg-red-500 text-white shadow-[0_0_0_2px_rgba(239,68,68,0.15)] animate-pulse">
                        {{ $_pendingNews }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.services.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.services.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                {{ __('admin.services') }}
            </a>

            <a href="{{ route('admin.offices.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.offices.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                {{ __('admin.offices') }}
            </a>

            <a href="{{ route('admin.quiz.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.quiz.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ __('admin.quiz') }}
            </a>

            <a href="{{ route('admin.road-signs.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.road-signs.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                {{ __('admin.road_signs') }}
            </a>

            <a href="{{ route('admin.emergency.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.emergency.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                {{ __('admin.emergency') }}
            </a>

            <a href="{{ route('admin.hospitals.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.hospitals.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                {{ __('admin.hospitals') }}
            </a>

            <a href="{{ route('admin.banners.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.banners.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ __('admin.banners') }}
            </a>

            <a href="{{ route('admin.social-services.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.social-services.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ __('admin.social_services') }}
            </a>

            <a href="{{ route('admin.vital-events.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.vital-events.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ __('admin.vital_events') }}
            </a>

            <a href="{{ route('admin.advisors.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.advisors.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                {{ __('admin.advisors') }}
            </a>

            <a href="{{ route('admin.notifications.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.notifications.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                {{ __('admin.notifications') }}
            </a>

            <a href="{{ route('admin.reminders.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.reminders.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ __('admin.reminders') }}
            </a>

            <a href="{{ route('admin.ai.logs') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.ai.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                {{ __('admin.ai_logs') }}
            </a>

            <a href="{{ route('admin.shorts.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.shorts.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                {{ __('admin.shorts') }}
            </a>

            <a href="{{ route('admin.ar-filters.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.ar-filters.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                AR Filters
            </a>
            @endif

            {{-- Verification --}}
            @if(!auth()->user()->isLearningAdmin())
            <div class="pt-1 pb-0.5 px-4">
                <p class="text-[10px] font-bold text-[#7a9a7a] uppercase tracking-widest">Verification</p>
            </div>

            <a href="{{ route('admin.verification.audit-logs') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.verification.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Doc Verification Logs
                @php($_pendingVerifications = Cache::store('array')->remember('_admin_verification_pending', 30, fn () => \App\Models\VerificationAuditLog::where('status','pending_authorization')->whereDate('created_at', today())->count()))
                @if($_pendingVerifications > 0)
                    <span class="ml-auto inline-flex items-center justify-center min-w-[22px] h-5 px-1.5 text-[11px] font-bold leading-none rounded-full bg-yellow-500 text-white">
                        {{ $_pendingVerifications }}
                    </span>
                @endif
            </a>
            @endif

            {{-- Learning Center --}}
            <div class="pt-1 pb-0.5 px-4">
                <p class="text-[10px] font-bold text-[#7a9a7a] uppercase tracking-widest">Learning Center</p>
            </div>

            <a href="{{ route('admin.learning.categories.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.learning.categories.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Categories
            </a>

            <a href="{{ route('admin.learning.chapters.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.learning.chapters.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Chapters
            </a>

            <a href="{{ route('admin.learning.mock-tests.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.learning.mock-tests.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Mock Tests
            </a>

            <a href="{{ route('admin.learning.competitions.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.learning.competitions.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
                Competitions
            </a>

            <a href="{{ route('admin.learning.daily-quiz.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.learning.daily-quiz.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Daily Quiz
            </a>

            <a href="{{ route('admin.learning.flashcards.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.learning.flashcards.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Flashcards
            </a>

            <a href="{{ route('admin.learning.achievements.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.learning.achievements.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
                Achievements
            </a>

            <a href="{{ route('admin.learning.programs.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.learning.programs.*') || request()->routeIs('admin.learning.courses.*') || request()->routeIs('admin.learning.subjects.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
                Programs & Courses
            </a>

            <a href="{{ route('admin.learning.live-sessions.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.learning.live-sessions.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                Live Sessions
            </a>

            @if(!auth()->user()->isLearningAdmin())
            <a href="{{ route('admin.settings.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.settings.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ __('admin.settings') }}
            </a>
            @endif
        </nav>

        <div class="px-3 py-4 border-t border-[#3F523F]">
            <div class="flex items-center gap-3 px-4 py-3 bg-[#3F523F] rounded-xl">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                    <span class="text-[#4A5D4A] font-semibold text-xs">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-white truncate text-sm">{{ auth()->user()->name ?? '' }}</p>
                    <p class="text-xs text-blue-200 truncate">{{ auth()->user()->email ?? '' }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}" class="mt-3">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-3 text-blue-100 hover:bg-[#3F523F] hover:text-white rounded-xl transition-all font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    {{ __('admin.logout') }}
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 ml-64 flex flex-col min-h-screen">
        {{-- Top Header --}}
        <header class="bg-white border-b border-gray-100 px-8 py-4 flex items-center justify-between sticky top-0 z-20">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">@yield('title', __('admin.dashboard'))</h1>
                <p class="text-xs text-gray-500 mt-0.5">@yield('subtitle', '')</p>
            </div>
            <div class="flex items-center gap-4">
                {{-- Language Switcher --}}
                <div class="flex items-center bg-gray-100 p-1 rounded-xl border border-gray-200">
                    <a href="{{ route('locale.switch', 'en') }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ app()->getLocale() == 'en' ? 'bg-[#4A5D4A] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        English
                    </a>
                    <a href="{{ route('locale.switch', 'ne') }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ app()->getLocale() == 'ne' ? 'bg-[#4A5D4A] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        नेपाली
                    </a>
                </div>

                <span class="text-sm text-gray-500">{{ now()->format('D, d M Y') }}</span>
            </div>
        </header>

        {{-- Flash Messages --}}
        <div class="px-8 pt-4">
            @if(session('success'))
                <div class="flex items-center gap-3 bg-[#E1E8E1] border border-[#4A5D4A] text-[#4A5D4A] rounded-xl px-4 py-3 text-sm mb-4">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 bg-[#E8DFE1] border border-[#5D4A4A] text-[#5D4A4A] rounded-xl px-4 py-3 text-sm mb-4">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293-1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586l-1.414-1.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif
        </div>

        {{-- Page Content --}}
        <main class="flex-1 px-8 pb-8 pt-6">
            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
