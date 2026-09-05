<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- Primary SEO Meta Tags --}}
    <title>{{ $program->title_en }} ({{ $program->title_ne }}) — Nagarik Learning Center</title>
    <meta name="description" content="{{ Str::limit(strip_tags($program->description_en), 160) }}">
    <meta name="keywords" content="{{ $program->title_en }}, {{ $program->title_ne }}, Nagarik Learning Center, Loksewa Nepal, Nagarik+">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph SEO --}}
    <meta property="og:locale" content="{{ app()->getLocale() == 'ne' ? 'ne_NP' : 'en_US' }}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $program->title_en }} — Nagarik Learning Center">
    <meta property="og:description" content="{{ Str::limit(strip_tags($program->description_en), 160) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="Nagarik Learning Center">
    <meta property="og:image" content="{{ $program->banner_image_url ? asset($program->banner_image_url) : asset('icon.png') }}">

    {{-- Twitter Card SEO --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $program->title_en }} — Nagarik Learning Center">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($program->description_en), 160) }}">
    <meta name="twitter:image" content="{{ $program->banner_image_url ? asset($program->banner_image_url) : asset('icon.png') }}">

    <link rel="icon" href="/icon.png" type="image/png">

    {{-- JSON-LD Schema.org Course Data --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@type": "Course",
      "name": "{{ addslashes($program->title_en) }}",
      "alternateName": "{{ addslashes($program->title_ne) }}",
      "description": "{{ addslashes(Str::limit(strip_tags($program->description_en), 250)) }}",
      "provider": {
        "@type": "Organization",
        "name": "Nagarik Learning Center",
        "sameAs": "{{ config('app.url', 'https://nagarikplus.techprocod.com.np') }}"
      },
      "isAccessibleForFree": {{ $program->is_free ? 'true' : 'false' }}
    }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', 'Noto Sans Devanagari', sans-serif; background: #f8fafc; }
        .accordion-body { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
        .accordion-body.open { max-height: 4000px; }
        .chapter-list { max-height: 0; overflow: hidden; transition: max-height 0.25s ease; }
        .chapter-list.open { max-height: 3000px; }
        .rotate-180 { transform: rotate(180deg); }
        .arrow { transition: transform 0.25s ease; }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased">

{{-- ── Nav ─────────────────────────────────────────────────────────────── --}}
<nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-xl border-b border-slate-200/80 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-16 sm:h-20">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('programs.index') }}"
               class="flex items-center gap-2 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/80 px-3 py-1.5 rounded-xl transition shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Nagarik Learning Center</span>
            </a>
            <span class="text-slate-300 shrink-0">/</span>
            <span class="text-sm font-bold text-slate-900 truncate">{{ $program->title_en }}</span>
        </div>

        {{-- Navigation Tools Menu (Desktop) --}}
        <div class="hidden lg:flex items-center gap-1 bg-slate-100/80 p-1.5 rounded-2xl border border-slate-200/60 shadow-inner">
            <a href="{{ route('programs.index') }}"
               class="px-3 py-1 rounded-xl text-xs font-bold transition-all flex items-center gap-1 bg-white text-emerald-700 shadow-xs border border-slate-200/60">
                <span class="text-sm">🎓</span>
                <span>Learning</span>
            </a>
            <a href="{{ route('user.pdf-tools') }}"
               class="px-3 py-1 rounded-xl text-xs font-bold transition-all flex items-center gap-1 text-slate-700 hover:text-emerald-700 hover:bg-white shadow-xs">
                <span class="text-sm">🛠️</span>
                <span>PDF Tools</span>
            </a>
            <a href="{{ route('user.file-transfer') }}"
               class="px-3 py-1 rounded-xl text-xs font-bold transition-all flex items-center gap-1 text-slate-700 hover:text-emerald-700 hover:bg-white shadow-xs">
                <span class="text-sm">⚡</span>
                <span>Quick Share</span>
            </a>
            <a href="{{ url('/#findHospitalsBtn') }}"
               class="px-3 py-1 rounded-xl text-xs font-bold transition-all flex items-center gap-1 text-slate-700 hover:text-emerald-700 hover:bg-white shadow-xs">
                <span class="text-sm">🏥</span>
                <span>Hospitals</span>
            </a>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            @auth
                <a href="{{ route('user.dashboard') }}" class="text-xs font-bold text-slate-700 hover:text-slate-900 px-3 py-2">Dashboard</a>
            @else
                <a href="{{ route('user.login') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 px-2 py-1.5">Login</a>
                <a href="{{ route('user.register') }}"
                   class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-xs">
                    Sign Up Free
                </a>
            @endauth
        </div>
    </div>
</nav>

<div class="max-w-7xl mx-auto px-4 py-8">
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

{{-- ── LEFT COLUMN ──────────────────────────────────────────────────────── --}}
<div class="lg:col-span-2 space-y-6">

    {{-- Hero card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Banner --}}
        <div class="relative h-52 overflow-hidden
            {{ $program->banner_url ? '' : 'bg-gradient-to-br from-green-600 to-green-400' }}">
            @if($program->banner_url)
                <img src="{{ $program->banner_url }}" alt="{{ $program->title_en }}"
                     class="w-full h-full object-cover">
            @else
                <div class="absolute inset-0 flex items-center justify-center opacity-20 text-white text-8xl">
                    {{ $program->icon ?? '📚' }}
                </div>
            @endif

            {{-- Guru count badge --}}
            @if($program->guru_count > 0)
            <div class="absolute bottom-3 right-3 flex items-center gap-1.5 bg-black/50 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1.5 rounded-full">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                </svg>
                {{ $program->guru_count }} Gurus
            </div>
            @endif
        </div>

        <div class="p-6">
            {{-- Badges --}}
            <div class="flex items-center gap-2 mb-3 flex-wrap">
                @if($program->category)
                    <span class="px-2.5 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                        {{ $program->category->icon }} {{ $program->category->name_en }}
                    </span>
                @endif
                @if($program->is_free)
                    <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">FREE</span>
                @else
                    <span class="px-2.5 py-0.5 bg-orange-100 text-orange-700 text-xs font-bold rounded-full">
                        Rs {{ number_format($program->price) }}
                    </span>
                @endif
            </div>

            <h1 class="text-2xl font-extrabold text-gray-900 leading-tight">{{ $program->title_en }}</h1>
            @if($program->title_np)
                <p class="text-sm text-gray-400 mt-0.5">{{ $program->title_np }}</p>
            @endif
            <p class="text-xs text-gray-400 mt-2">Last updated {{ $program->updated_at->format('n/Y') }}</p>

            {{-- Description with Show More --}}
            <div class="mt-4">
                <div id="desc-short" class="text-sm text-gray-700 leading-relaxed">
                    {!! nl2br(e(Str::limit($program->description_en, 300))) !!}
                </div>
                @if(strlen($program->description_en) > 300)
                <div id="desc-full" class="hidden text-sm text-gray-700 leading-relaxed">
                    {!! nl2br(e($program->description_en)) !!}
                </div>
                <button onclick="toggleDesc()" id="desc-btn"
                        class="mt-2 text-green-600 font-semibold text-sm hover:underline">
                    Show More ▾
                </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Our Features --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-bold text-gray-900 mb-4">Our Features</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach([
                ['🔴', 'Live Classes'],
                ['✏️', 'Handwritten Notes'],
                ['🎬', 'Recorded Videos'],
                ['📝', 'Chapterwise MCQs'],
                ['💬', 'Personal Feedback'],
                ['🧑‍🏫', 'Ask Guru Any Time'],
            ] as [$icon, $label])
            <div class="flex items-center gap-2.5 bg-green-50 border border-green-100 rounded-xl px-3 py-3">
                <span class="text-xl leading-none">{{ $icon }}</span>
                <span class="text-sm font-semibold text-gray-800">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Courses accordion ───────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-bold text-gray-900 mb-5">Courses</h2>

        @forelse($program->courses as $ci => $course)
        <div class="mb-3 border border-gray-100 rounded-xl overflow-hidden">

            {{-- Course header --}}
            <button type="button"
                    onclick="toggleCourse({{ $course->id }})"
                    class="w-full flex items-center justify-between bg-gray-50 hover:bg-gray-100 px-5 py-3.5 transition">
                <span class="font-bold text-gray-900 text-sm text-left">{{ $course->title_en }}</span>
                <svg id="carrow-{{ $course->id }}"
                     class="w-4 h-4 text-gray-400 arrow shrink-0 ml-2 {{ $ci === 0 ? 'rotate-180' : '' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            {{-- Subjects --}}
            <div id="course-{{ $course->id }}" class="accordion-body {{ $ci === 0 ? 'open' : '' }}">
                <div class="divide-y divide-gray-50">
                    @forelse($course->subjects as $subject)

                    {{-- Subject row --}}
                    <div>
                        <button type="button"
                                onclick="toggleSubject({{ $subject->id }})"
                                class="w-full flex items-center gap-3 px-5 py-3 hover:bg-green-50 transition group">

                            {{-- Icon --}}
                            @if($subject->icon)
                                <span class="text-xl shrink-0">{{ $subject->icon }}</span>
                            @else
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold text-white shrink-0"
                                     style="background: {{ $subject->color_code ?? '#16a34a' }}">
                                    {{ strtoupper(substr($subject->title_en, 0, 2)) }}
                                </div>
                            @endif

                            {{-- Title --}}
                            <span class="flex-1 text-sm font-semibold text-gray-800 text-left group-hover:text-green-700 transition">
                                {{ $subject->title_en }}
                                @if($subject->title_np)
                                    <span class="font-normal text-gray-400 ml-1">/ {{ $subject->title_np }}</span>
                                @endif
                            </span>

                            {{-- Counts — exactly like Ambition Guru --}}
                            <div class="flex items-center gap-3 shrink-0">
                                {{-- Total lectures/chapters --}}
                                @if($subject->chapter_count > 0)
                                <span class="text-xs font-bold text-gray-700 bg-gray-100 px-2 py-0.5 rounded-full min-w-[28px] text-center">
                                    {{ $subject->chapter_count }}
                                </span>
                                @endif
                                {{-- Video count --}}
                                @if($subject->video_count > 0)
                                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full min-w-[28px] text-center" title="Videos">
                                    {{ $subject->video_count }}
                                </span>
                                @elseif($subject->chapter_count > 0)
                                <span class="text-xs text-gray-300 min-w-[28px] text-center">0</span>
                                @endif

                                <svg id="sarrow-{{ $subject->id }}"
                                     class="w-3.5 h-3.5 text-gray-400 arrow"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </button>

                        {{-- Chapter list inside subject --}}
                        <div id="subject-{{ $subject->id }}" class="chapter-list">
                            <div class="bg-gray-50 divide-y divide-gray-100/80 pl-4">
                                @forelse($subject->publishedChapters as $chapter)
                                <div class="flex items-center gap-3 px-4 py-2.5 hover:bg-white transition">
                                    {{-- Type icon --}}
                                    @php
                                        $typeIcons = [
                                            'lecture'   => ['🔴', 'text-red-500'],
                                            'video'     => ['🎬', 'text-blue-500'],
                                            'note'      => ['📝', 'text-gray-400'],
                                            'model_set' => ['📋', 'text-purple-500'],
                                            'audio'     => ['🎧', 'text-orange-400'],
                                        ];
                                        [$typeEmoji, $typeColor] = $typeIcons[$chapter->content_type] ?? ['📝', 'text-gray-400'];
                                    @endphp
                                    <span class="text-sm shrink-0" title="{{ $chapter->typeLabel() }}">{{ $typeEmoji }}</span>
                                    <span class="text-xs text-gray-700 flex-1 leading-snug">{{ $chapter->title_en }}</span>
                                    @if($chapter->duration_minutes)
                                        <span class="text-xs text-gray-400 shrink-0">{{ $chapter->duration_minutes }}m</span>
                                    @elseif($chapter->read_time_minutes)
                                        <span class="text-xs text-gray-400 shrink-0">{{ $chapter->read_time_minutes }}m</span>
                                    @endif
                                </div>
                                @empty
                                <div class="px-6 py-3 text-xs text-gray-400 italic">No chapters published yet.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    @empty
                    <div class="px-5 py-4 text-sm text-gray-400">No subjects added yet.</div>
                    @endforelse
                </div>
            </div>

        </div>
        @empty
        <p class="text-gray-400 text-sm">No courses added yet.</p>
        @endforelse
    </div>

    {{-- Today's Routine --}}
    @if($todaysSessions->count())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
            Today's Routine
            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
        </h2>
        <div class="space-y-3">
            @foreach($todaysSessions as $session)
            <div class="flex gap-4 p-4 rounded-xl border border-gray-100 hover:border-green-200 transition">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center shrink-0 overflow-hidden">
                    @if($session->instructor_avatar)
                        <img src="{{ $session->instructor_avatar }}" class="w-10 h-10 object-cover">
                    @else
                        <span class="text-green-700 font-bold text-sm">
                            {{ strtoupper(substr($session->instructor_name ?? 'G', 0, 1)) }}
                        </span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-gray-500">
                        {{ $session->starts_at->format('h:i A') }}
                        @if($session->duration_minutes)
                            — {{ \Carbon\Carbon::parse($session->starts_at)->addMinutes($session->duration_minutes)->format('h:i A') }}
                        @endif
                        @if($session->status === 'live')
                            <span class="text-red-600 animate-pulse ml-1">● LIVE</span>
                        @endif
                    </p>
                    @if($session->instructor_name)
                        <p class="text-sm font-bold text-gray-900">{{ $session->instructor_name }}</p>
                    @endif
                    <p class="text-xs text-gray-600 mt-0.5 line-clamp-2">{{ $session->title_en }}</p>
                </div>
                @if($session->is_free)
                    <span class="shrink-0 self-start mt-1 px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">
                        Free
                    </span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- ── RIGHT SIDEBAR ────────────────────────────────────────────────────── --}}
<div class="lg:col-span-1 space-y-5">

    {{-- Enroll / Price card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sticky top-20">

        @if($program->thumbnail_url)
            <div class="rounded-xl overflow-hidden mb-4 h-40">
                <img src="{{ $program->thumbnail_url }}" alt="{{ $program->title_en }}"
                     class="w-full h-full object-cover">
            </div>
        @endif

        {{-- Price --}}
        <div class="text-2xl font-extrabold mb-4">
            @if($program->is_free)
                <span class="text-green-600">Free</span>
            @else
                <span class="text-gray-900">Rs {{ number_format($program->price) }}</span>
            @endif
        </div>

        @auth
            <button class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition text-sm">
                Enroll Now
            </button>
        @else
            <a href="{{ route('user.register') }}"
               class="block w-full py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition text-sm text-center">
                Sign Up Free to Enroll
            </a>
            <a href="{{ route('user.login') }}"
               class="block w-full mt-2 py-2.5 border border-gray-200 hover:border-green-400 text-gray-700 font-semibold rounded-xl transition text-sm text-center">
                Login
            </a>
        @endauth

        {{-- Stats — exactly matching Ambition Guru sidebar --}}
        <div class="mt-5 pt-4 border-t border-gray-100 space-y-2.5">

            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Enrolled</span>
                <span class="font-bold text-gray-900">{{ number_format($program->enrolled_count) }} Students</span>
            </div>

            @if($program->total_lectures > 0)
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Lectures</span>
                <span class="font-bold text-gray-900">{{ number_format($program->total_lectures) }}</span>
            </div>
            @endif

            @if($program->total_videos > 0)
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Video Lessons</span>
                <span class="font-bold text-gray-900">{{ $program->video_time }}</span>
            </div>
            @endif

            @if($program->total_notes > 0)
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Notes</span>
                <span class="font-bold text-gray-900">{{ number_format($program->total_notes) }}</span>
            </div>
            @endif

            @if($program->total_model_sets > 0)
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Model Sets</span>
                <span class="font-bold text-gray-900">{{ number_format($program->total_model_sets) }}</span>
            </div>
            @endif

            @php
                // Count unique subjects across all courses
                $subjectCount = $program->courses->sum(fn($c) => $c->subjects->count());
            @endphp
            @if($subjectCount > 0)
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">No. of Subjects</span>
                <span class="font-bold text-gray-900">{{ $subjectCount }} Subjects</span>
            </div>
            @endif

            @if($program->guru_count > 0)
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Gurus</span>
                <span class="font-bold text-gray-900">{{ $program->guru_count }} Instructors</span>
            </div>
            @endif

        </div>

        {{-- Guru names list --}}
        @if(!empty($program->guru_names))
        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-xs font-semibold text-gray-500 mb-2">Instructors</p>
            <div class="flex flex-wrap gap-2">
                @foreach($program->guru_names as $guru)
                <span class="flex items-center gap-1.5 text-xs bg-gray-50 border border-gray-100 text-gray-700 px-2.5 py-1 rounded-full font-medium">
                    <span class="w-5 h-5 rounded-full bg-green-200 text-green-800 flex items-center justify-center text-xs font-bold">
                        {{ strtoupper(substr($guru, 0, 1)) }}
                    </span>
                    {{ $guru }}
                </span>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- Similar Courses --}}
    @if($similarPrograms->count())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-bold text-gray-900 text-sm mb-4">Similar Courses</h3>
        <div class="space-y-3">
            @foreach($similarPrograms as $sim)
            <a href="{{ route('programs.show', $sim->slug) }}"
               class="flex gap-3 p-3 rounded-xl hover:bg-green-50 transition group">
                <div class="w-14 h-14 rounded-xl overflow-hidden shrink-0 bg-green-100 flex items-center justify-center">
                    @if($sim->thumbnail_url)
                        <img src="{{ $sim->thumbnail_url }}" class="w-14 h-14 object-cover">
                    @else
                        <span class="text-2xl">{{ $sim->icon ?? '📚' }}</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-gray-900 group-hover:text-green-700 line-clamp-2 leading-snug">
                        {{ $sim->title_en }}
                    </p>
                    @if($sim->title_np)
                        <p class="text-xs text-gray-400 mt-0.5">{{ $sim->title_np }}</p>
                    @endif
                    <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ Str::limit($sim->description_en, 60) }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>
{{-- end grid --}}
</div>
</div>

{{-- Footer --}}
<footer class="mt-16 bg-gray-900 text-gray-400 py-10 px-4">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
            <div class="flex items-center gap-2 mb-3">
                <img src="/icon.png" alt="Nagarik+" class="w-8 h-8 rounded-lg">
                <span class="text-white font-bold text-lg">Nagarik+</span>
            </div>
            <p class="text-sm leading-relaxed">Your Gateway to Global Success.</p>
            <p class="text-sm mt-3">support@nagarikplus.com</p>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-3">Company</h4>
            <ul class="space-y-2 text-sm">
                <li><a href="#" class="hover:text-white transition">About Us</a></li>
                <li><a href="{{ route('programs.index') }}" class="hover:text-white transition">Courses</a></li>
                <li><a href="#" class="hover:text-white transition">Blogs</a></li>
                <li><a href="#" class="hover:text-white transition">Audios</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-3">Legal</h4>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('privacy-policy') }}" class="hover:text-white transition">Privacy Policy</a></li>
                <li><a href="{{ route('delete-account') }}" class="hover:text-white transition">Delete Account</a></li>
            </ul>
        </div>
    </div>
</footer>

<script>
// Description toggle
function toggleDesc() {
    const s = document.getElementById('desc-short');
    const f = document.getElementById('desc-full');
    const b = document.getElementById('desc-btn');
    if (f.classList.contains('hidden')) {
        f.classList.remove('hidden'); s.classList.add('hidden');
        b.textContent = 'Show Less ▴';
    } else {
        f.classList.add('hidden'); s.classList.remove('hidden');
        b.textContent = 'Show More ▾';
    }
}

// Course accordion
function toggleCourse(id) {
    const el = document.getElementById('course-' + id);
    const arrow = document.getElementById('carrow-' + id);
    el.classList.toggle('open');
    arrow.classList.toggle('rotate-180');
}

// Subject accordion
function toggleSubject(id) {
    const el = document.getElementById('subject-' + id);
    const arrow = document.getElementById('sarrow-' + id);
    el.classList.toggle('open');
    arrow.classList.toggle('rotate-180');
}
</script>
</body>
</html>
