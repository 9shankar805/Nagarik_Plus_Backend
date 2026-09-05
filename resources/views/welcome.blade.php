<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- Primary SEO Meta Tags --}}
    <title>Nagarik+ (नागरिक+) — Premier Citizen Services & Learning Portal in Nepal</title>
    <meta name="description" content="Nagarik+ (नागरिक+) is Nepal's all-in-one digital citizen platform offering Nagarik Learning Center (Loksewa & Driving License exam practice), free PDF tools, P2P file share, emergency hospital finder, and civic support.">
    <meta name="keywords" content="Nagarik+, Nagarik App Nepal, Nagarik Learning Center, Loksewa preparation Nepal, Driving license exam Nepal, PDF tools Nepal, Quick Share, Citizen services Nepal, नागरिक एप">
    <meta name="author" content="Nagarik+ Nepal">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph / Facebook SEO --}}
    <meta property="og:locale" content="{{ app()->getLocale() == 'ne' ? 'ne_NP' : 'en_US' }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Nagarik+ (नागरिक+) — Premier Citizen Services & Learning Portal in Nepal">
    <meta property="og:description" content="Access free Loksewa civil service courses, Driving License exam practice, PDF tools, P2P file transfer, and emergency services in Nepal.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="Nagarik+ (नागरिक+)">
    <meta property="og:image" content="{{ asset('icon.png') }}">

    {{-- Twitter Card SEO --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Nagarik+ (नागरिक+) — Digital Citizen Services Nepal">
    <meta name="twitter:description" content="Access free Loksewa civil service courses, Driving License exam practice, PDF tools, P2P file transfer, and emergency services in Nepal.">
    <meta name="twitter:image" content="{{ asset('icon.png') }}">

    <link rel="icon" href="/icon.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

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
    <style>
        body {
            font-family: 'Noto Sans Devanagari', 'Inter', sans-serif;
            background-color: #f8fafc; /* slate-50 */
        }
        
        .card-hover {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .btn-primary {
            background-color: #4f46e5; /* indigo-600 */
            transition: background-color 0.2s ease;
        }
        
        .btn-primary:hover {
            background-color: #4338ca; /* indigo-700 */
        }
        
        .btn-secondary {
            background-color: #ffffff;
            border: 1px solid #cbd5e1; /* slate-300 */
            transition: background-color 0.2s ease;
        }
        
        .btn-secondary:hover {
            background-color: #f1f5f9; /* slate-100 */
        }
    </style>
    <x-firebase-init />
</head>
<body class="min-h-screen text-slate-800 antialiased selection:bg-indigo-100 selection:text-indigo-900">
    <!-- Navigation -->
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
                        <span class="font-extrabold text-slate-900 text-xl tracking-tight leading-none">{{ __('welcome.app_name') }}</span>
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
                    <a href="#findHospitalsBtn"
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
                        <a href="{{ route('user.login') }}" class="text-xs text-slate-600 hover:text-slate-900 font-bold transition-colors px-2 py-1.5">{{ __('welcome.login') }}</a>
                        <a href="{{ route('user.register') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs px-4 py-2 rounded-xl font-bold shadow-sm transition-all">{{ __('welcome.register') }}</a>
                        <div class="w-px h-5 bg-slate-200 mx-1"></div>
                        <a href="{{ route('admin.login') }}" class="text-xs text-slate-500 hover:text-slate-800 font-medium transition-colors">{{ __('welcome.admin_portal') }}</a>
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
                <a href="#findHospitalsBtn"
                   class="px-3 py-1.5 rounded-xl font-bold text-slate-700 bg-slate-100 hover:bg-emerald-100 hover:text-emerald-800 whitespace-nowrap flex items-center gap-1 shrink-0">
                    🏥 Hospitals
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="py-20 lg:py-28 bg-white border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-8 items-center">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 bg-indigo-50 border border-indigo-100 px-3 py-1.5 rounded-md mb-8">
                        <span class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></span>
                        <span class="text-indigo-700 text-xs font-semibold uppercase tracking-wide">{{ __('welcome.trusted_by') }}</span>
                    </div>
                    <h1 class="text-5xl lg:text-6xl font-bold text-slate-900 mb-6 leading-[1.1] tracking-tight">
                        {{ __('welcome.hero_title_1') }} <span class="text-indigo-600">{{ __('welcome.hero_title_2') }}</span>
                    </h1>
                    <p class="text-lg lg:text-xl text-slate-600 mb-8 leading-relaxed">
                        {{ __('welcome.hero_subtitle') }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('user.register') }}" class="btn-primary text-white px-6 py-3 rounded-lg font-medium text-center shadow-sm">
                            {{ __('welcome.get_started') }}
                        </a>
                        <a href="#features" class="btn-secondary text-slate-700 px-6 py-3 rounded-lg font-medium text-center">
                            {{ __('welcome.explore_features') }}
                        </a>
                    </div>
                    <div class="mt-10 flex items-center gap-4 text-sm text-slate-500">
                        <div class="flex -space-x-2">
                            <div class="w-8 h-8 rounded-full bg-slate-100 border-2 border-white flex items-center justify-center text-slate-600 font-semibold text-xs">A</div>
                            <div class="w-8 h-8 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center text-slate-600 font-semibold text-xs">S</div>
                            <div class="w-8 h-8 rounded-full bg-slate-300 border-2 border-white flex items-center justify-center text-slate-700 font-semibold text-xs">K</div>
                        </div>
                        <span>{{ __('welcome.join_thousands') }}</span>
                    </div>
                </div>
                <div class="hidden lg:block relative">
                    <!-- Clean geometric background element -->
                    <div class="absolute inset-0 bg-slate-50 rounded-[2rem] transform rotate-3 scale-105 border border-slate-100"></div>
                    <div class="relative bg-white rounded-2xl p-8 shadow-xl shadow-slate-200/50 border border-slate-100">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-slate-50 rounded-xl p-5 text-center border border-slate-100 card-hover cursor-default">
                                <p class="text-3xl font-bold text-indigo-600 mb-1">12.5K</p>
                                <p class="text-sm text-slate-600 font-medium">{{ __('welcome.active_users') }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-5 text-center border border-slate-100 card-hover cursor-default">
                                <p class="text-3xl font-bold text-indigo-600 mb-1">68K</p>
                                <p class="text-sm text-slate-600 font-medium">{{ __('welcome.docs_stored') }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-5 text-center border border-slate-100 card-hover cursor-default">
                                <p class="text-3xl font-bold text-indigo-600 mb-1">250+</p>
                                <p class="text-sm text-slate-600 font-medium">{{ __('welcome.services') }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-5 text-center border border-slate-100 card-hover cursor-default">
                                <div class="flex items-center justify-center gap-1 mb-1">
                                    <span class="text-3xl font-bold text-indigo-600">4.9</span>
                                    <span class="text-xl text-yellow-400">★</span>
                                </div>
                                <p class="text-sm text-slate-600 font-medium">{{ __('welcome.user_rating') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 lg:py-28 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 max-w-2xl mx-auto">
                <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-4 tracking-tight">{{ __('welcome.features_title') }}</h2>
                <p class="text-lg text-slate-600">
                    {{ __('welcome.features_subtitle') }}
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Feature 1 -->
                <div class="bg-white rounded-xl p-6 border border-slate-200 card-hover">
                    <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ __('welcome.doc_locker') }}</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">{{ __('welcome.doc_locker_desc') }}</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-xl p-6 border border-slate-200 card-hover">
                    <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ __('welcome.reminders') }}</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">{{ __('welcome.reminders_desc') }}</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-xl p-6 border border-slate-200 card-hover">
                    <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ __('welcome.guides') }}</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">{{ __('welcome.guides_desc') }}</p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white rounded-xl p-6 border border-slate-200 card-hover">
                    <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ __('welcome.news') }}</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">{{ __('welcome.news_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Nagarik Learning Center — Courses Section -->
    @php
        $featuredPrograms = \App\Models\Program::published()
            ->with('category:id,name_en,icon,color_code')
            ->orderBy('display_order')
            ->limit(6)
            ->get();

        $todaysSessions = \App\Models\LiveSession::where('status', 'live')
            ->orWhere(function($q) {
                $q->where('status', 'scheduled')->whereDate('starts_at', today());
            })
            ->orderBy('starts_at')
            ->limit(5)
            ->get();
    @endphp

    @if($featuredPrograms->count())
    <section class="py-20 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Section header --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200/80 px-3.5 py-1.5 rounded-full mb-3 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-emerald-800 text-xs font-bold uppercase tracking-wider">Nagarik Learning Center</span>
                        <span class="text-slate-400">•</span>
                        <span class="text-emerald-700 text-xs font-medium font-devanagari">नागरिक लर्निङ सेन्टर</span>
                    </div>
                    <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">
                        Learn Anything, <span class="text-emerald-600">Anytime</span>
                    </h2>
                    <p class="text-slate-600 mt-2 text-base max-w-2xl">
                        Comprehensive preparation portal for Nepal Civil Service (Loksewa), Driving License, Legal Awareness & Financial Education.
                    </p>
                </div>
                <a href="{{ route('programs.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-sm hover:shadow-emerald-500/20">
                    <span>Explore All Courses</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            {{-- Today's live routine (if any) --}}
            @if($todaysSessions->count())
            <div class="mb-10 bg-slate-900 text-white border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                <div class="absolute -top-12 -right-12 w-48 h-48 bg-red-500/10 blur-3xl rounded-full"></div>
                <h3 class="text-xs font-bold text-red-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                    Today's Live Routine Schedule
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($todaysSessions as $session)
                    <div class="flex items-center gap-3 bg-slate-800/90 backdrop-blur-md rounded-xl p-3 border border-slate-700/60 shadow-sm">
                        <div class="w-9 h-9 rounded-full bg-red-950/80 border border-red-500/30 flex items-center justify-center text-red-400 font-bold text-xs shrink-0">
                            @if($session->instructor_avatar)
                                <img src="{{ $session->instructor_avatar }}" class="w-9 h-9 rounded-full object-cover">
                            @else
                                {{ strtoupper(substr($session->instructor_name ?? 'L', 0, 1)) }}
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-red-400">{{ $session->starts_at->format('h:i A') }}</p>
                            <p class="text-xs font-bold text-white truncate mt-0.5">{{ $session->title_en }}</p>
                            @if($session->instructor_name)
                                <p class="text-[11px] text-slate-400 truncate">{{ $session->instructor_name }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Program cards grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                @foreach($featuredPrograms as $program)
                @php
                    $slug = strtolower($program->slug ?? '');
                    $title = strtolower($program->title_en ?? '');

                    if (str_contains($slug, 'driving') || str_contains($title, 'driving')) {
                        $bgGradient = 'from-emerald-600 via-teal-700 to-slate-900';
                    } elseif (str_contains($slug, 'loksewa') || str_contains($title, 'loksewa')) {
                        $bgGradient = 'from-indigo-700 via-blue-800 to-slate-900';
                    } elseif (str_contains($slug, 'finance') || str_contains($title, 'finance')) {
                        $bgGradient = 'from-teal-600 via-cyan-800 to-slate-900';
                    } elseif (str_contains($slug, 'law') || str_contains($slug, 'rights') || str_contains($title, 'law')) {
                        $bgGradient = 'from-amber-600 via-emerald-800 to-slate-900';
                    } else {
                        $bgGradient = 'from-purple-700 via-slate-800 to-slate-900';
                    }
                @endphp

                <a href="{{ route('programs.show', $program->slug) }}"
                   class="group bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 overflow-hidden flex flex-col">

                    {{-- Thumbnail --}}
                    <div class="relative h-44 overflow-hidden bg-gradient-to-br {{ $bgGradient }} flex items-center justify-center p-6">
                        @if($program->thumbnail_url)
                            <img src="{{ $program->thumbnail_url }}" alt="{{ $program->title_en }}"
                                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="text-5xl filter drop-shadow-md transform group-hover:scale-110 transition-transform duration-300">
                                {{ $program->icon ?? '📚' }}
                            </div>
                        @endif

                        {{-- Free / Price badge --}}
                        @if($program->is_free)
                            <span class="absolute top-3.5 right-3.5 px-3 py-1 bg-emerald-500/90 backdrop-blur-md text-white text-[11px] font-black uppercase tracking-wider rounded-full shadow-md border border-emerald-400/40">
                                FREE
                            </span>
                        @else
                            <span class="absolute top-3.5 right-3.5 px-3 py-1 bg-white/95 backdrop-blur-md text-slate-900 text-xs font-extrabold rounded-full shadow-md border border-white">
                                Rs {{ number_format($program->price) }}
                            </span>
                        @endif

                        {{-- Category --}}
                        @if($program->category)
                            <span class="absolute bottom-3.5 left-3.5 px-3 py-1 bg-slate-900/75 backdrop-blur-md text-white text-xs font-semibold rounded-full border border-white/20 flex items-center gap-1.5 shadow-sm">
                                <span>{{ $program->category->icon }}</span>
                                <span>{{ $program->category->name_en }}</span>
                            </span>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="p-6 flex flex-col flex-1">
                        <h3 class="font-extrabold text-slate-900 text-lg leading-snug mb-1 group-hover:text-emerald-600 transition-colors line-clamp-2">
                            {{ $program->title_en }}
                        </h3>
                        @if($program->title_np)
                            <span class="text-xs font-semibold text-emerald-800 font-devanagari bg-emerald-50 border border-emerald-200/60 px-2.5 py-0.5 rounded-md inline-block mb-2 self-start">
                                {{ $program->title_np }}
                            </span>
                        @endif
                        <p class="text-xs text-slate-600 leading-relaxed line-clamp-2 mb-4">
                            {{ $program->description_en }}
                        </p>

                        {{-- Features mini icons --}}
                        <div class="flex gap-1.5 flex-wrap mb-5 mt-auto">
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

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-500 font-medium flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                                </svg>
                                {{ number_format($program->enrolled_count) }} enrolled
                            </span>
                            <span class="text-emerald-600 font-bold group-hover:text-emerald-700 flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                                View Course
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Stats bar --}}
            <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach([
                    ['🎓', 'Free Preparation', $featuredPrograms->where('is_free', true)->count() . '+ Courses Free'],
                    ['🔴', 'Live Sessions', 'Daily Interactive Classes'],
                    ['📹', 'Recorded Library', 'Full HD VOD Lessons'],
                    ['🧑‍🏫', 'Certified Gurus', 'Verified Q&A Assistance'],
                ] as [$icon, $label, $sub])
                <div class="bg-white border border-slate-200/80 shadow-xs rounded-2xl p-5 text-center">
                    <div class="text-3xl mb-2">{{ $icon }}</div>
                    <div class="font-extrabold text-slate-900 text-sm">{{ $label }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">{{ $sub }}</div>
                </div>
                @endforeach
            </div>

        </div>
    </section>
    @endif

    <!-- Free Tools Section -->
    <section class="py-16 bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-indigo-600 rounded-2xl p-8 lg:p-12 text-white shadow-xl shadow-indigo-600/20 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="max-w-2xl">
                    <div class="inline-block bg-indigo-500/50 backdrop-blur text-indigo-50 px-3 py-1.5 rounded-md text-sm font-semibold mb-4 border border-indigo-400/50">
                        100% Free & Open
                    </div>
                    <h2 class="text-3xl font-bold mb-4">Powerful PDF & Image Utilities</h2>
                    <p class="text-indigo-100 text-lg leading-relaxed">
                        Compress, convert, resize, split, merge, and watermark your files instantly. Completely free, secure, and no account required.
                    </p>
                </div>
                <div>
                    <a href="{{ route('user.pdf-tools') }}" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-slate-50 px-8 py-4 rounded-xl font-bold text-lg shadow-lg transition-all hover:-translate-y-1">
                        Use Free Tools Now
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Quick Share Block -->
            <div class="mt-8 bg-green-600 rounded-2xl p-8 lg:p-12 text-white shadow-xl shadow-green-600/20 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="max-w-2xl">
                    <div class="inline-block bg-green-500/50 backdrop-blur text-green-50 px-3 py-1.5 rounded-md text-sm font-semibold mb-4 border border-green-400/50">
                        PC to Mobile
                    </div>
                    <h2 class="text-3xl font-bold mb-4">Quick File Transfer</h2>
                    <p class="text-green-100 text-lg leading-relaxed">
                        Instantly send documents, photos, or any file from your PC directly to your Nagarik+ Mobile App using a simple 6-digit PIN.
                    </p>
                </div>
                <div>
                    <a href="{{ route('user.file-transfer') }}" class="inline-flex items-center gap-2 bg-white text-green-700 hover:bg-slate-50 px-8 py-4 rounded-xl font-bold text-lg shadow-lg transition-all hover:-translate-y-1">
                        Transfer a File
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Nearby Hospitals Section -->
    <section class="py-16 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-red-100 text-red-600 rounded-full mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-slate-900 mb-3 tracking-tight">Emergency Nearby Facilities</h2>
                <p class="text-slate-600 max-w-2xl mx-auto mb-8">Instantly find the nearest hospitals, clinics, pharmacies, and blood banks around your current location with one click.</p>
                <button id="findHospitalsBtn" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-bold shadow-md transition-colors inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Locate Facilities Near Me
                </button>
            </div>

            <div id="hospitalsLoading" class="hidden text-center text-slate-500 py-8">
                <svg class="animate-spin h-8 w-8 text-red-500 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Searching for medical facilities...
            </div>

            <div id="hospitalsError" class="hidden text-center text-red-600 bg-red-50 rounded-xl p-4 max-w-xl mx-auto font-medium"></div>

            <div id="hospitalsContainer" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 hidden">
                <!-- Dynamic cards injected here -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-slate-500 text-sm">&copy; {{ date('Y') }} {{ __('welcome.app_name') }}. All rights reserved.</p>
            <div class="flex items-center gap-6 text-sm text-slate-500">
                <span>Made with <span class="text-red-500">❤️</span> in Nepal</span>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('findHospitalsBtn').addEventListener('click', function() {
            const btn = this;
            const loading = document.getElementById('hospitalsLoading');
            const errorContainer = document.getElementById('hospitalsError');
            const container = document.getElementById('hospitalsContainer');

            // Reset UI
            errorContainer.classList.add('hidden');
            container.classList.add('hidden');
            container.innerHTML = '';
            
            if (!navigator.geolocation) {
                errorContainer.innerText = 'Geolocation is not supported by your browser.';
                errorContainer.classList.remove('hidden');
                return;
            }

            // Show loading
            btn.classList.add('hidden');
            loading.classList.remove('hidden');

            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    try {
                        // Try 5km first, fall back to 50km if nothing nearby
                        let res = await fetch(`/api/v1/hospitals/nearby?lat=${lat}&lng=${lng}&radius=5`);
                        if (!res.ok) throw new Error('API error: ' + res.status);
                        let json = await res.json();

                        if (!json.success) throw new Error('API returned failure.');

                        // Nothing in 5km — widen to 50km
                        if (!json.data || json.data.length === 0) {
                            res = await fetch(`/api/v1/hospitals/nearby?lat=${lat}&lng=${lng}&radius=50`);
                            if (!res.ok) throw new Error('API error: ' + res.status);
                            json = await res.json();
                        }

                        // Still nothing — show the full list sorted by distance
                        if (!json.data || json.data.length === 0) {
                            res = await fetch(`/api/v1/hospitals?limit=6`);
                            if (!res.ok) throw new Error('API error: ' + res.status);
                            json = await res.json();
                            if (!json.data || json.data.length === 0) {
                                throw new Error('No medical facilities found in our database.');
                            }
                        }

                        // Take top 6 nearest facilities
                        const topFacilities = json.data.slice(0, 6);
                        
                        topFacilities.forEach(facility => {
                            const distanceStr = facility.distance_km !== null ? `<span class="text-sm font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md">${facility.distance_km} km away</span>` : '';
                            const mapUrl = `https://www.google.com/maps/dir/?api=1&destination=${facility.latitude},${facility.longitude}`;
                            const typeBadge = `<span class="text-xs font-semibold uppercase tracking-wider text-slate-500 border border-slate-200 rounded px-2 py-0.5">${facility.type.replace('_', ' ')}</span>`;
                            
                            const card = `
                                <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm card-hover flex flex-col justify-between">
                                    <div>
                                        <div class="flex justify-between items-start mb-2">
                                            ${typeBadge}
                                            ${distanceStr}
                                        </div>
                                        <h3 class="font-bold text-slate-900 text-lg mb-3">${facility.name}</h3>
                                        ${facility.address ? `
                                        <p class="text-sm text-slate-600 flex items-start gap-2 mb-4">
                                            <svg class="w-4 h-4 text-slate-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            ${facility.address}
                                        </p>
                                        ` : ''}
                                    </div>
                                    <a href="${mapUrl}" target="_blank" class="mt-4 w-full text-center bg-slate-50 hover:bg-slate-100 text-slate-700 font-medium py-2 rounded-lg border border-slate-200 transition-colors flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                                        Get Directions
                                    </a>
                                </div>
                            `;
                            container.insertAdjacentHTML('beforeend', card);
                        });

                        loading.classList.add('hidden');
                        container.classList.remove('hidden');
                        btn.innerHTML = "Update My Location";
                        btn.classList.remove('hidden');
                        
                    } catch (err) {
                        loading.classList.add('hidden');
                        btn.classList.remove('hidden');
                        errorContainer.innerText = 'Failed to fetch nearby hospitals. Please try again later.';
                        errorContainer.classList.remove('hidden');
                    }
                },
                (error) => {
                    loading.classList.add('hidden');
                    btn.classList.remove('hidden');
                    let msg = 'Failed to get your location.';
                    if (error.code === error.PERMISSION_DENIED) msg = 'You denied the request for Geolocation. Please enable it in your browser settings to find nearby hospitals.';
                    errorContainer.innerText = msg;
                    errorContainer.classList.remove('hidden');
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });
    </script>
</body>
</html>
