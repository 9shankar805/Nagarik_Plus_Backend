<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- Primary SEO Meta Tags --}}
    <title>@yield('title', 'Nagarik Learning Center & Citizen Portal') — Nagarik+</title>
    <meta name="description" content="@yield('meta_description', 'Nagarik+ (नागरिक+) is Nepal\'s premier citizen services platform providing Loksewa preparation, Driving License exam practice, free PDF compress & edit tools, P2P file share, and nearby hospital finder.')">
    <meta name="keywords" content="@yield('meta_keywords', 'Nagarik+, Nagarik Learning Center, Loksewa Nepal, Driving License Exam Nepal, Free PDF Tools Nepal, P2P File Transfer, Citizen Services Nepal, नागरिक, लोकसेवा तयारी')">
    <meta name="author" content="Nagarik+ Nepal">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph / Facebook SEO --}}
    <meta property="og:locale" content="{{ app()->getLocale() == 'ne' ? 'ne_NP' : 'en_US' }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'Nagarik Learning Center & Citizen Portal') — Nagarik+">
    <meta property="og:description" content="@yield('meta_description', 'Free Loksewa civil service preparation, Driving License written & trial exam practice, PDF utilities, Quick Share, and Citizen Services in Nepal.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="Nagarik+ (नागरिक+)">
    <meta property="og:image" content="{{ asset('icon.png') }}">
    <meta property="og:image:width" content="512">
    <meta property="og:image:height" content="512">

    {{-- Twitter Card SEO --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Nagarik Learning Center & Citizen Portal') — Nagarik+">
    <meta name="twitter:description" content="@yield('meta_description', 'Free Loksewa civil service preparation, Driving License written & trial exam practice, PDF utilities, Quick Share, and Citizen Services in Nepal.')">
    <meta name="twitter:image" content="{{ asset('icon.png') }}">

    <link rel="icon" href="/icon.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Noto Sans Devanagari', 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        .btn-primary {
            background-color: #059669;
            transition: background-color 0.2s ease;
        }
        .btn-primary:hover {
            background-color: #047857;
        }
    </style>

    {{-- JSON-LD Schema.org Structured Data --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@type": "WebApplication",
      "name": "Nagarik+",
      "alternateName": "नागरिक+",
      "url": "{{ config('app.url', 'https://nagarikplus.techprocod.com.np') }}",
      "applicationCategory": "Civic & Educational Utility",
      "operatingSystem": "Web, Android, iOS",
      "description": "Nepal's premier digital citizen services platform providing Nagarik Learning Center (Loksewa & Driving License), free PDF utilities, P2P file transfer, and emergency hospital locator.",
      "publisher": {
        "@type": "Organization",
        "name": "Nagarik+",
        "logo": "{{ asset('icon.png') }}"
      }
    }
    </script>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-firebase-init />
</head>
<body class="min-h-screen text-slate-800 antialiased selection:bg-indigo-100 selection:text-indigo-900 flex flex-col">

    <!-- Top Navigation Bar -->
    <nav class="bg-white/90 backdrop-blur-xl border-b border-slate-200/80 sticky top-0 z-50 transition-all shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 md:h-20 items-center">
                {{-- Logo --}}
                <a href="/" class="flex items-center gap-3 shrink-0 group">
                    <div class="relative flex items-center justify-center">
                        <img src="/icon.png" alt="Nagarik+" class="w-10 h-10 rounded-xl shadow-md group-hover:scale-105 transition-transform duration-300">
                        <span class="absolute -bottom-1 -right-1 flex h-3.5 w-3.5 items-center justify-center rounded-full bg-emerald-500 ring-2 ring-white text-[8px] text-white font-bold">✓</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-extrabold text-slate-900 text-xl tracking-tight leading-none">Nagarik<span class="text-emerald-600">+</span></span>
                        <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider mt-0.5">Citizen Platform</span>
                    </div>
                </a>

                {{-- Tools Menu (Desktop) --}}
                <div class="hidden lg:flex items-center gap-1 bg-slate-100/80 p-1.5 rounded-2xl border border-slate-200/60 shadow-inner">
                    <a href="{{ route('programs.index') }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 text-slate-700 hover:text-emerald-700 hover:bg-white shadow-xs">
                        <span class="text-sm">🎓</span>
                        <span>Learning Center</span>
                    </a>
                    <a href="{{ route('user.pdf-tools') }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 text-slate-700 hover:text-emerald-700 hover:bg-white shadow-xs">
                        <span class="text-sm">🛠️</span>
                        <span>PDF Tools</span>
                    </a>
                    <a href="{{ route('user.file-transfer') }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 text-slate-700 hover:text-emerald-700 hover:bg-white shadow-xs">
                        <span class="text-sm">⚡</span>
                        <span>Quick Share</span>
                    </a>
                    <a href="{{ url('/#findHospitalsBtn') }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 text-slate-700 hover:text-emerald-700 hover:bg-white shadow-xs">
                        <span class="text-sm">🏥</span>
                        <span>Nearby Hospitals</span>
                    </a>
                </div>

                {{-- Language & Auth --}}
                <div class="flex items-center gap-3">
                    {{-- Language Switcher --}}
                    <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200/80">
                        <a href="{{ route('locale.switch', 'en') }}"
                           class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all {{ app()->getLocale() == 'en' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                            EN
                        </a>
                        <a href="{{ route('locale.switch', 'ne') }}"
                           class="px-2.5 py-1 rounded-lg text-xs font-bold font-devanagari transition-all {{ app()->getLocale() == 'ne' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                            नेपाली
                        </a>
                    </div>

                    <div class="hidden sm:flex items-center gap-3">
                        <a href="{{ route('user.login') }}" class="text-xs text-slate-600 hover:text-slate-900 font-bold transition-colors px-2 py-1.5">Login</a>
                        <a href="{{ route('user.register') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs px-4 py-2 rounded-xl font-bold shadow-sm transition-all">Sign Up Free</a>
                    </div>
                </div>
            </div>

            {{-- Tools Menu (Mobile Bar) --}}
            <div class="lg:hidden flex items-center justify-between gap-1 overflow-x-auto py-2.5 border-t border-slate-100 text-xs">
                <a href="{{ route('programs.index') }}"
                   class="px-3 py-1.5 rounded-xl font-bold text-slate-700 bg-slate-100 hover:bg-emerald-100 hover:text-emerald-800 whitespace-nowrap flex items-center gap-1 shrink-0">
                    🎓 Learning Center
                </a>
                <a href="{{ route('user.pdf-tools') }}"
                   class="px-3 py-1.5 rounded-xl font-bold text-slate-700 bg-slate-100 hover:bg-emerald-100 hover:text-emerald-800 whitespace-nowrap flex items-center gap-1 shrink-0">
                    🛠️ PDF Tools
                </a>
                <a href="{{ route('user.file-transfer') }}"
                   class="px-3 py-1.5 rounded-xl font-bold text-slate-700 bg-slate-100 hover:bg-emerald-100 hover:text-emerald-800 whitespace-nowrap flex items-center gap-1 shrink-0">
                    ⚡ Quick Share
                </a>
                <a href="{{ url('/#findHospitalsBtn') }}"
                   class="px-3 py-1.5 rounded-xl font-bold text-slate-700 bg-slate-100 hover:bg-emerald-100 hover:text-emerald-800 whitespace-nowrap flex items-center gap-1 shrink-0">
                    🏥 Hospitals
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-slate-500 text-sm">&copy; {{ date('Y') }} {{ __('welcome.app_name') }}. All rights reserved.</p>
            <div class="flex items-center gap-6 text-sm text-slate-500">
                <span>Made with <span class="text-red-500">❤️</span> in Nepal</span>
            </div>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>
