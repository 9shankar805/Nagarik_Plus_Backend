<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nagarik+ Admin — @yield('title', 'Dashboard')</title>
    <link rel="icon" href="/icon.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #D8E0D8 0%, #E1E8E1 100%);
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="fixed top-0 left-0 h-full w-64 flex flex-col z-30 bg-[#4A5D4A]">
        <div class="flex items-center gap-3 px-6 py-5 border-b border-[#3F523F]">
            <img src="/icon.png" alt="Nagarik+" class="w-10 h-10 rounded-xl object-cover">
            <span class="text-white font-bold text-xl">Nagarik+ Admin</span>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.dashboard') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.users.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Users
            </a>

            <a href="{{ route('admin.documents.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.documents.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Documents
            </a>

            <a href="{{ route('admin.news.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.news.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                News
            </a>

            <a href="{{ route('admin.services.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.services.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Citizen Services
            </a>

            <a href="{{ route('admin.offices.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.offices.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Offices
            </a>

            <a href="{{ route('admin.quiz.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.quiz.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Quiz Questions
            </a>

            <a href="{{ route('admin.road-signs.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.road-signs.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Road Signs
            </a>

            <a href="{{ route('admin.emergency.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.emergency.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                Emergency Contacts
            </a>

            <a href="{{ route('admin.hospitals.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.hospitals.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Hospitals
            </a>

            <a href="{{ route('admin.banners.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.banners.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Home Banners
            </a>

            <a href="{{ route('admin.social-services.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.social-services.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Social Services Cards
            </a>

            <a href="{{ route('admin.vital-events.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.vital-events.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Vital Events Cards
            </a>

            <a href="{{ route('admin.advisors.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.advisors.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Nagarik Advisors
            </a>

            <a href="{{ route('admin.notifications.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.notifications.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Push Broadcast
            </a>

            <a href="{{ route('admin.reminders.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.reminders.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Reminders
            </a>

            <a href="{{ route('admin.ai.logs') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.ai.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                AI Assistant Logs
            </a>

            <a href="{{ route('admin.shorts.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.shorts.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                Video Shorts / Tutorials
            </a>

            <a href="{{ route('admin.settings.index') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#3F523F] text-blue-100 font-medium transition-all
                  {{ request()->routeIs('admin.settings.*') ? 'bg-[#3F523F] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Settings
            </a>
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
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 ml-64 flex flex-col min-h-screen">
        {{-- Top Header --}}
        <header class="bg-white border-b border-gray-100 px-8 py-4 flex items-center justify-between sticky top-0 z-20">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
                <p class="text-xs text-gray-500 mt-0.5">@yield('subtitle', '')</p>
            </div>
            <div class="flex items-center gap-4">
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
