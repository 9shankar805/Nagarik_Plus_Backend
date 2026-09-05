<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- Primary SEO --}}
    <title>Nagarik Learning Center (नागरिक लर्निङ सेन्टर) — Free Loksewa & Driving License Prep</title>
    <meta name="description" content="Nagarik Learning Center (नागरिक लर्निङ सेन्टर) by Nagarik+ offers free Loksewa civil service preparation, Driving License written & trial guides, legal awareness, financial literacy, and live interactive routine in Nepal.">
    <meta name="keywords" content="Nagarik Learning Center, Loksewa preparation Nepal, Driving License written exam Nepal, Trial exam guide Nepal, नागरिक लर्निङ सेन्टर, लोकसेवा तयारी">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph SEO --}}
    <meta property="og:locale" content="{{ app()->getLocale() == 'ne' ? 'ne_NP' : 'en_US' }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Nagarik Learning Center (नागरिक लर्निङ सेन्टर) — Nagarik+">
    <meta property="og:description" content="Free Loksewa civil service preparation, Driving License written & trial guides, and civic education in Nepal.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="Nagarik Learning Center">
    <meta property="og:image" content="{{ asset('icon.png') }}">

    {{-- Twitter Card SEO --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Nagarik Learning Center (नागरिक लर्निङ सेन्टर)">
    <meta name="twitter:description" content="Free Loksewa civil service preparation, Driving License written & trial guides, and civic education in Nepal.">
    <meta name="twitter:image" content="{{ asset('icon.png') }}">

    <link rel="icon" href="/icon.png" type="image/png">

    {{-- JSON-LD Schema.org EducationalOrganization --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@type": "EducationalOrganization",
      "name": "Nagarik Learning Center",
      "alternateName": "नागरिक लर्निङ सेन्टर",
      "url": "{{ url()->current() }}",
      "description": "Educational platform providing free Loksewa preparation, driving license trial guides, and civic awareness in Nepal.",
      "parentOrganization": {
        "@type": "Organization",
        "name": "Nagarik+",
        "url": "{{ config('app.url', 'https://nagarikplus.techprocod.com.np') }}"
      }
    }
    </script>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '"Noto Sans Devanagari"', 'sans-serif'],
                        heading: ['"Outfit"', '"Plus Jakarta Sans"', 'sans-serif'],
                        devanagari: ['"Noto Sans Devanagari"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                            950: '#022c22',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', 'Noto Sans Devanagari', sans-serif; background-color: #f8fafc; }
        .hero-glow {
            background: radial-gradient(circle at 50% 0%, rgba(16, 185, 129, 0.18) 0%, rgba(15, 23, 42, 0) 70%);
        }
        .card-shine {
            position: relative;
            overflow: hidden;
        }
        .card-shine::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(60deg, transparent 30%, rgba(255,255,255,0.08) 50%, transparent 70%);
            transform: rotate(30deg) translateY(-100%);
            transition: transform 0.8s ease;
        }
        .card-shine:hover::after {
            transform: rotate(30deg) translateY(100%);
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 antialiased selection:bg-emerald-500 selection:text-white">

<!-- ── STICKY NAVBAR ────────────────────────────────────────────────────────── -->
<nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-xl border-b border-slate-200/80 shadow-xs transition-all">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 md:h-20">
            
            {{-- Brand Logo & Title --}}
            <a href="/" class="flex items-center gap-3 shrink-0 group">
                <div class="relative flex items-center justify-center">
                    <img src="/icon.png" alt="Nagarik+" class="w-10 h-10 rounded-xl shadow-md group-hover:scale-105 transition-transform duration-300">
                    <span class="absolute -bottom-1 -right-1 flex h-3.5 w-3.5 items-center justify-center rounded-full bg-emerald-500 ring-2 ring-white text-[8px] text-white font-bold">✓</span>
                </div>
                <div class="flex flex-col">
                    <div class="flex items-center gap-2">
                        <span class="font-heading font-extrabold text-slate-900 text-xl tracking-tight">Nagarik<span class="text-emerald-600">+</span></span>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-700 font-devanagari leading-none mt-0.5">नागरिक लर्निङ सेन्टर</span>
                </div>
            </a>

            {{-- Navigation Tools Menu (Desktop) --}}
            <div class="hidden lg:flex items-center gap-1 bg-slate-100/80 p-1.5 rounded-2xl border border-slate-200/60 shadow-inner">
                <a href="{{ route('programs.index') }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-white text-emerald-700 shadow-xs border border-slate-200/60">
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

            {{-- Nav Controls & Quick Links --}}
            <div class="flex items-center gap-3 sm:gap-4">
                {{-- Language Switcher --}}
                <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200/80">
                    <a href="{{ route('locale.switch', 'en') }}"
                       class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all {{ app()->getLocale() == 'en' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-500 hover:text-slate-900' }}">
                        EN
                    </a>
                    <a href="{{ route('locale.switch', 'ne') }}"
                       class="px-2.5 py-1 rounded-lg text-xs font-bold font-devanagari transition-all {{ app()->getLocale() == 'ne' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-500 hover:text-slate-900' }}">
                        नेपाली
                    </a>
                </div>

                @auth
                    <a href="{{ route('user.dashboard') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition-all shadow-xs">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('user.login') }}" class="hidden sm:inline-block text-xs font-semibold text-slate-600 hover:text-slate-900 px-2 py-1.5 transition">
                        Login
                    </a>
                    <a href="{{ route('user.register') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow-emerald-500/20 transition-all">
                        Sign Up Free
                    </a>
                @endauth
            </div>
        </div>

        {{-- Mobile Navigation Tools Bar --}}
        <div class="lg:hidden flex items-center justify-between gap-1 overflow-x-auto py-2.5 border-t border-slate-100 text-xs">
            <a href="{{ route('programs.index') }}"
               class="px-3 py-1.5 rounded-xl font-bold bg-emerald-100 text-emerald-800 whitespace-nowrap flex items-center gap-1 shrink-0">
                🎓 Learning Hub
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

<!-- ── HERO SECTION ──────────────────────────────────────────────────────────── -->
<header class="relative bg-slate-900 text-white overflow-hidden hero-glow py-16 md:py-24 border-b border-slate-800">
    {{-- Decorative Background Elements --}}
    <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[800px] h-[350px] bg-emerald-500/10 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-teal-500/10 blur-[100px] rounded-full pointer-events-none"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center">
            
            {{-- Official Badge --}}
            <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-slate-800/80 border border-slate-700/80 backdrop-blur-md shadow-inner mb-6">
                <span class="flex h-2.5 w-2.5 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>
                <span class="text-xs font-extrabold text-emerald-400 uppercase tracking-wider font-heading">Nagarik Learning Center</span>
                <span class="text-slate-600">•</span>
                <span class="text-xs font-medium text-slate-300 font-devanagari">नागरिक लर्निङ सेन्टर</span>
            </div>

            {{-- Main Title --}}
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-heading font-black tracking-tight text-white leading-[1.15] mb-6">
                Learn Anything, <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 via-teal-300 to-cyan-300">Anytime</span>
            </h1>

            {{-- Subtitle --}}
            <p class="text-base sm:text-lg text-slate-300 leading-relaxed font-normal mb-8 max-w-2xl mx-auto">
                Comprehensive preparation portal for Nepal Civil Service (Loksewa), Driving License written & trial tests, Legal Rights & Financial Education with live classes, notes & mock exams.
            </p>

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('programs.index') }}" class="relative max-w-xl mx-auto mb-10">
                <div class="relative flex items-center bg-white/10 backdrop-blur-xl p-1.5 rounded-2xl border border-white/20 shadow-2xl focus-within:border-emerald-400/80 focus-within:ring-4 focus-within:ring-emerald-500/20 transition-all">
                    <div class="pl-4 text-slate-400 pointer-events-none">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search Loksewa, Driving License, Law, Finance courses..."
                           class="w-full bg-transparent px-3 py-3 text-white placeholder-slate-400 text-sm focus:outline-none font-medium">
                    <button type="submit" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold rounded-xl text-sm transition-all shadow-md shrink-0 flex items-center gap-1.5">
                        Search
                    </button>
                </div>
            </form>

            {{-- Quick Stats Pills --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center border-t border-slate-800/80 pt-8 max-w-4xl mx-auto">
                <div class="bg-slate-800/50 backdrop-blur-md rounded-xl p-3 border border-slate-700/50">
                    <p class="text-emerald-400 font-heading font-extrabold text-lg sm:text-xl">100% Free</p>
                    <p class="text-slate-400 text-xs mt-0.5">Civil Prep & Mocks</p>
                </div>
                <div class="bg-slate-800/50 backdrop-blur-md rounded-xl p-3 border border-slate-700/50">
                    <p class="text-emerald-400 font-heading font-extrabold text-lg sm:text-xl">Daily Live</p>
                    <p class="text-slate-400 text-xs mt-0.5">Interactive Routines</p>
                </div>
                <div class="bg-slate-800/50 backdrop-blur-md rounded-xl p-3 border border-slate-700/50">
                    <p class="text-emerald-400 font-heading font-extrabold text-lg sm:text-xl">1,000+ MCQs</p>
                    <p class="text-slate-400 text-xs mt-0.5">Practice Questions</p>
                </div>
                <div class="bg-slate-800/50 backdrop-blur-md rounded-xl p-3 border border-slate-700/50">
                    <p class="text-emerald-400 font-heading font-extrabold text-lg sm:text-xl">Expert Gurus</p>
                    <p class="text-slate-400 text-xs mt-0.5">Verified Curriculum</p>
                </div>
            </div>

        </div>
    </div>
</header>

<!-- ── MAIN CONTENT CONTAINER ──────────────────────────────────────────────── -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">

    {{-- Today's Live Sessions Banner (if any) --}}
    @if(isset($todaysSessions) && $todaysSessions->count())
    <div class="mb-12 bg-gradient-to-r from-red-950/40 via-red-900/20 to-slate-900 border border-red-500/30 rounded-2xl p-6 shadow-lg relative overflow-hidden">
        <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
            <div class="flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                </span>
                <h3 class="text-base font-extrabold text-white font-heading tracking-wide">Today's Live Routine</h3>
                <span class="px-2 py-0.5 bg-red-500/20 text-red-400 text-xs font-bold rounded-md border border-red-500/30">
                    {{ $todaysSessions->count() }} Sessions Scheduled
                </span>
            </div>
            <span class="text-xs text-slate-400">Join live interactive classes directly with Gurus</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($todaysSessions as $session)
            <div class="bg-slate-900/90 rounded-xl p-4 border border-red-500/20 flex gap-3 shadow-md">
                <div class="w-10 h-10 rounded-full bg-red-950 flex items-center justify-center shrink-0 border border-red-500/30 text-red-400 font-bold text-sm">
                    @if($session->instructor_avatar)
                        <img src="{{ $session->instructor_avatar }}" class="w-10 h-10 rounded-full object-cover">
                    @else
                        {{ strtoupper(substr($session->instructor_name ?? 'L', 0, 1)) }}
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-1">
                        <span class="text-xs font-bold text-red-400">🔴 {{ $session->starts_at->format('h:i A') }}</span>
                        @if($session->status === 'live')
                            <span class="text-[10px] bg-red-500 text-white font-extrabold px-2 py-0.5 rounded-full animate-pulse">LIVE NOW</span>
                        @endif
                    </div>
                    <p class="text-sm font-bold text-white leading-tight mt-0.5 truncate">{{ $session->title_en }}</p>
                    @if($session->instructor_name)
                        <p class="text-xs text-slate-400 mt-0.5">{{ $session->instructor_name }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Filter Header Bar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-2xl md:text-3xl font-heading font-extrabold text-slate-900 tracking-tight">
                    Nagarik Learning Programs
                </h2>
                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full">
                    {{ $programs->total() }} Courses
                </span>
            </div>
            <p class="text-slate-500 text-xs md:text-sm mt-1">Select a program to start studying live classes, recorded VODs, notes, and interactive quizzes.</p>
        </div>

        {{-- Filters & Tabs --}}
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('programs.index') }}"
               class="px-3.5 py-2 text-xs font-bold rounded-xl border transition-all {{ !request('category') && !request('free') ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:border-emerald-400' }}">
                All Courses
            </a>
            <a href="{{ route('programs.index', array_merge(request()->query(), ['free' => '1'])) }}"
               class="px-3.5 py-2 text-xs font-bold rounded-xl border transition-all {{ request('free') === '1' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:border-emerald-400' }}">
                Free Only
            </a>

            @if(isset($categories) && $categories->count())
                @foreach($categories as $cat)
                <a href="{{ route('programs.index', array_merge(request()->query(), ['category' => $cat->id])) }}"
                   class="px-3.5 py-2 text-xs font-bold rounded-xl border transition-all {{ request('category') == $cat->id ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:border-emerald-400' }}">
                    {{ $cat->icon }} {{ $cat->name_en }}
                </a>
                @endforeach
            @endif

            @if(request()->hasAny(['search','free','category']))
                <a href="{{ route('programs.index') }}" class="px-3.5 py-2 text-xs font-bold rounded-xl bg-slate-200 text-slate-700 hover:bg-slate-300 transition">
                    Clear Filters
                </a>
            @endif
        </div>
    </div>

    {{-- Course Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
        @forelse($programs as $program)
        @php
            // Assign gradient & icon theme based on program title/slug
            $slug = strtolower($program->slug ?? '');
            $title = strtolower($program->title_en ?? '');

            if (str_contains($slug, 'driving') || str_contains($title, 'driving')) {
                $bgGradient = 'from-emerald-600 via-teal-700 to-slate-900';
                $accentColor = 'emerald';
                $defaultSvgIcon = '<svg class="w-12 h-12 text-emerald-200/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h8m-8 4h8m-8 4h8M4 6h16a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z"/></svg>';
            } elseif (str_contains($slug, 'loksewa') || str_contains($title, 'loksewa')) {
                $bgGradient = 'from-indigo-700 via-blue-800 to-slate-900';
                $accentColor = 'indigo';
                $defaultSvgIcon = '<svg class="w-12 h-12 text-indigo-200/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V3"/></svg>';
            } elseif (str_contains($slug, 'finance') || str_contains($title, 'finance')) {
                $bgGradient = 'from-teal-600 via-cyan-800 to-slate-900';
                $accentColor = 'teal';
                $defaultSvgIcon = '<svg class="w-12 h-12 text-teal-200/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            } elseif (str_contains($slug, 'law') || str_contains($slug, 'rights') || str_contains($title, 'law')) {
                $bgGradient = 'from-amber-600 via-emerald-800 to-slate-900';
                $accentColor = 'amber';
                $defaultSvgIcon = '<svg class="w-12 h-12 text-amber-200/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 6l9-4 9 4v6c0 5.55-3.84 10.74-9 12-5.16-1.26-9-5.45-9-12V6z"/></svg>';
            } else {
                $bgGradient = 'from-purple-700 via-slate-800 to-slate-900';
                $accentColor = 'purple';
                $defaultSvgIcon = '<svg class="w-12 h-12 text-purple-200/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>';
            }
        @endphp

        <a href="{{ route('programs.show', $program->slug) }}"
           class="group bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 overflow-hidden flex flex-col card-shine">

            {{-- Thumbnail Header --}}
            <div class="relative h-44 overflow-hidden bg-gradient-to-br {{ $bgGradient }} flex items-center justify-center p-6">
                @if($program->thumbnail_url)
                    <img src="{{ $program->thumbnail_url }}" alt="{{ $program->title_en }}"
                         class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-slate-900/30 group-hover:bg-slate-900/20 transition-colors"></div>
                @else
                    {{-- Decorative Icon Graphic --}}
                    <div class="relative z-10 flex flex-col items-center justify-center text-center">
                        @if($program->icon)
                            <span class="text-5xl filter drop-shadow-md mb-1 transform group-hover:scale-110 transition-transform duration-300">{{ $program->icon }}</span>
                        @else
                            {!! $defaultSvgIcon !!}
                        @endif
                    </div>
                @endif

                {{-- Price / FREE Badge --}}
                @if($program->is_free)
                    <span class="absolute top-3.5 right-3.5 px-3 py-1 bg-emerald-500/90 backdrop-blur-md text-white text-[11px] font-black uppercase tracking-wider rounded-full shadow-md border border-emerald-400/40">
                        FREE
                    </span>
                @else
                    <span class="absolute top-3.5 right-3.5 px-3 py-1 bg-white/95 backdrop-blur-md text-slate-900 text-xs font-extrabold rounded-full shadow-md border border-white">
                        Rs {{ number_format($program->price) }}
                    </span>
                @endif

                {{-- Category Pill --}}
                @if($program->category)
                    <span class="absolute bottom-3.5 left-3.5 px-3 py-1 bg-slate-900/75 backdrop-blur-md text-white text-xs font-semibold rounded-full border border-white/20 flex items-center gap-1.5 shadow-sm">
                        <span>{{ $program->category->icon }}</span>
                        <span>{{ $program->category->name_en }}</span>
                    </span>
                @endif
            </div>

            {{-- Card Body --}}
            <div class="p-6 flex-1 flex flex-col">
                <div class="mb-2">
                    <h3 class="font-heading font-extrabold text-slate-900 text-lg leading-snug group-hover:text-emerald-600 transition-colors line-clamp-2">
                        {{ $program->title_en }}
                    </h3>
                    @if($program->title_np)
                        <span class="text-xs font-semibold text-emerald-800 font-devanagari bg-emerald-50 border border-emerald-200/60 px-2.5 py-0.5 rounded-md inline-block mt-1.5">
                            {{ $program->title_np }}
                        </span>
                    @endif
                </div>

                <p class="text-xs text-slate-600 leading-relaxed line-clamp-2 mb-4">
                    {{ $program->description_en }}
                </p>

                {{-- Feature Badges --}}
                <div class="flex items-center gap-1.5 flex-wrap mb-5 mt-auto">
                    <span class="text-[11px] bg-slate-100 text-slate-700 font-semibold px-2.5 py-1 rounded-lg border border-slate-200/60 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Live
                    </span>
                    <span class="text-[11px] bg-slate-100 text-slate-700 font-semibold px-2.5 py-1 rounded-lg border border-slate-200/60 flex items-center gap-1">
                        📹 Videos
                    </span>
                    <span class="text-[11px] bg-slate-100 text-slate-700 font-semibold px-2.5 py-1 rounded-lg border border-slate-200/60 flex items-center gap-1">
                        📝 MCQs
                    </span>
                    <span class="text-[11px] bg-slate-100 text-slate-700 font-semibold px-2.5 py-1 rounded-lg border border-slate-200/60 flex items-center gap-1">
                        💬 Ask Guru
                    </span>
                </div>

                {{-- Card Footer --}}
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500 font-medium flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                        {{ number_format($program->enrolled_count) }} enrolled
                    </span>
                    <span class="text-emerald-600 font-bold group-hover:text-emerald-700 flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                        Explore Course
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </span>
                </div>

            </div>
        </a>
        @empty
        <div class="col-span-1 md:col-span-2 lg:col-span-3 py-20 text-center bg-white rounded-2xl border border-slate-200">
            <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">📚</div>
            <h3 class="text-lg font-bold text-slate-800">No courses match your query</h3>
            <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">Try searching for other terms like Loksewa, Driving License, or clear filters.</p>
            @if(request()->hasAny(['search','free','category']))
                <a href="{{ route('programs.index') }}" class="inline-block mt-4 px-4 py-2 bg-emerald-600 text-white font-bold text-xs rounded-xl hover:bg-emerald-700 transition">
                    Clear Search & View All
                </a>
            @endif
        </div>
        @endforelse
    </div>

    {{-- Pagination Links --}}
    <div class="mt-12 flex justify-center">
        {{ $programs->links() }}
    </div>

    <!-- ── WHY NAGARIK LEARNING CENTER ────────────────────────────────────── -->
    <section class="mt-20 pt-16 border-t border-slate-200">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="text-xs font-extrabold uppercase text-emerald-600 tracking-wider font-heading bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full">
                Citizen Knowledge Portal
            </span>
            <h2 class="text-3xl md:text-4xl font-heading font-black text-slate-900 tracking-tight mt-3">
                Why Study with Nagarik Learning Center?
            </h2>
            <p class="text-slate-600 text-sm md:text-base mt-2">
                Designed specifically for Nepali citizens to excel in public service examinations, driving license procedures, and essential civil knowledge.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-2xl mb-4">🏛️</div>
                <h3 class="font-heading font-bold text-slate-900 text-base mb-2">Loksewa Preparation</h3>
                <p class="text-xs text-slate-600 leading-relaxed"> Kharidar, Nayab Subba, Section Officer & State PSC mock tests with full answer keys.</p>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-xl bg-teal-50 border border-teal-100 flex items-center justify-center text-2xl mb-4">🚗</div>
                <h3 class="font-heading font-bold text-slate-900 text-base mb-2">Driving License Tests</h3>
                <p class="text-xs text-slate-600 leading-relaxed">Complete written question bank for Category A (Bike), B (Car), K (Scooter) with traffic sign quizzes.</p>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-2xl mb-4">⚖️</div>
                <h3 class="font-heading font-bold text-slate-900 text-base mb-2">Rights & Legal Awareness</h3>
                <p class="text-xs text-slate-600 leading-relaxed">Understand constitutional rights, labor law, consumer protection, and official application procedures.</p>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-2xl mb-4">🔴</div>
                <h3 class="font-heading font-bold text-slate-900 text-base mb-2">Live Routines & VOD</h3>
                <p class="text-xs text-slate-600 leading-relaxed">Attend live virtual classes with subject experts or watch recorded video lessons anytime.</p>
            </div>
        </div>
    </section>

</main>

<!-- ── FOOTER ────────────────────────────────────────────────────────────────── -->
<footer class="bg-slate-950 text-slate-400 py-16 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-10">
        
        <div class="md:col-span-2">
            <div class="flex items-center gap-3 mb-4">
                <img src="/icon.png" alt="Nagarik+" class="w-9 h-9 rounded-xl">
                <div>
                    <span class="text-white font-heading font-extrabold text-lg">Nagarik<span class="text-emerald-500">+</span> Learning Center</span>
                    <p class="text-xs text-emerald-400 font-devanagari">नागरिक लर्निङ सेन्टर</p>
                </div>
            </div>
            <p class="text-xs text-slate-400 leading-relaxed max-w-md">
                Empowering Nepal's citizens with digital access to public service prep, driving trial guides, legal literacy, and official digital document locker.
            </p>
            <p class="text-xs text-slate-500 mt-4">&copy; {{ date('Y') }} Nagarik+. All rights reserved. Made with ❤️ for Nepal.</p>
        </div>

        <div>
            <h4 class="text-white font-heading font-bold text-sm mb-4">Quick Links</h4>
            <ul class="space-y-2.5 text-xs">
                <li><a href="{{ route('programs.index') }}" class="hover:text-emerald-400 transition">All Courses</a></li>
                <li><a href="{{ route('user.pdf-tools') }}" class="hover:text-emerald-400 transition">Free PDF Tools</a></li>
                <li><a href="{{ route('user.file-transfer') }}" class="hover:text-emerald-400 transition">Quick Share (P2P)</a></li>
                <li><a href="/" class="hover:text-emerald-400 transition">Nagarik+ Home</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-white font-heading font-bold text-sm mb-4">Legal & Support</h4>
            <ul class="space-y-2.5 text-xs">
                <li><a href="{{ route('privacy-policy') }}" class="hover:text-emerald-400 transition">Privacy Policy</a></li>
                <li><a href="{{ route('delete-account') }}" class="hover:text-emerald-400 transition">Delete Account</a></li>
                <li class="pt-2 text-slate-500">Contact: support@nagarikplus.com</li>
            </ul>
        </div>

    </div>
</footer>

</body>
</html>
