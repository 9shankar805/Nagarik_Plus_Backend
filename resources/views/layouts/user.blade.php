<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nagarik+ — @yield('title', 'Dashboard')</title>
    <link rel="icon" href="/icon.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #ecfdf5 100%);
        }
    </style>
</head>
<body class="min-h-screen flex">

    @auth
    {{-- Sidebar (Only rendered for logged in users) --}}
    <aside class="fixed top-0 left-0 h-full w-64 flex flex-col z-30 bg-white border-r border-gray-100">
        <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100">
            <img src="/icon.png" alt="Nagarik+" class="w-10 h-10 rounded-xl object-cover">
            <span class="text-gray-800 font-bold text-xl">Nagarik+</span>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            <a href="{{ route('user.dashboard') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl bg-[#E1E8E1] text-[#4A5D4A] font-medium transition-all
                  {{ request()->routeIs('user.dashboard') ? 'bg-[#4A5D4A] text-white' : 'hover:bg-[#E1E8E1] hover:text-[#4A5D4A]' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('user.documents') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#E1E8E1] hover:text-[#4A5D4A] text-gray-600 font-medium transition-all
                  {{ request()->routeIs('user.documents') ? 'bg-[#4A5D4A] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Digital Locker
            </a>

            <a href="{{ route('user.pdf-tools') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#E1E8E1] hover:text-[#4A5D4A] text-gray-600 font-medium transition-all
                  {{ request()->routeIs('user.pdf-tools') ? 'bg-[#4A5D4A] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                PDF Tools
            </a>

            <a href="{{ route('user.guides') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#E1E8E1] hover:text-[#4A5D4A] text-gray-600 font-medium transition-all
                  {{ request()->routeIs('user.guides') ? 'bg-[#4A5D4A] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Guides
            </a>

            <a href="{{ route('user.news') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#E1E8E1] hover:text-[#4A5D4A] text-gray-600 font-medium transition-all
                  {{ request()->routeIs('user.news') ? 'bg-[#4A5D4A] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                News
            </a>

            <a href="{{ route('user.learning') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#E1E8E1] hover:text-[#4A5D4A] text-gray-600 font-medium transition-all
                  {{ request()->routeIs('user.learning') ? 'bg-[#4A5D4A] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Learning
            </a>

            <a href="{{ route('user.profile') }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-[#E1E8E1] hover:text-[#4A5D4A] text-gray-600 font-medium transition-all
                  {{ request()->routeIs('user.profile') ? 'bg-[#4A5D4A] text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Profile & Settings
            </a>
        </nav>

        <div class="px-3 py-4 border-t border-gray-100">
            <div class="flex items-center gap-3 px-4 py-3 bg-[#E1E8E1] rounded-xl">
                <div class="w-10 h-10 bg-[#4A5D4A] rounded-full flex items-center justify-center">
                    <span class="text-white font-semibold text-xs">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-800 truncate text-sm">{{ auth()->user()->name ?? 'User' }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? '' }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('user.logout') }}" class="mt-3">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-[#E1E8E1] hover:text-[#4A5D4A] rounded-xl transition-all font-medium text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>
    @endauth

    {{-- Main Content Container (Full width ml-0 for guests, ml-64 for logged-in users) --}}
    <div class="flex-1 {{ auth()->check() ? 'ml-64' : 'ml-0 w-full' }} flex flex-col min-h-screen">
        
        @auth
        {{-- Top Header (Only for logged in users) --}}
        <header class="bg-white border-b border-gray-100 px-8 py-4 flex items-center justify-between sticky top-0 z-20">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
                <p class="text-xs text-gray-500 mt-0.5">@yield('subtitle', '')</p>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500">{{ now()->format('D, d M Y') }}</span>
            </div>
        </header>
        @endauth

        {{-- Flash Messages --}}
        @if(auth()->check())
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
        @endif

        {{-- Page Content --}}
        <main class="flex-1 {{ auth()->check() ? 'px-8 pb-8 pt-6' : 'p-0' }}">
            @yield('content')
        </main>
    </div>

    @livewireScripts
    @yield('scripts')
</body>
</html>
