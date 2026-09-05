@extends('layouts.user')

@section('title', 'Download eNID Card')
@section('subtitle', 'Get your digital National Identity Card from DONIDCR')

@section('content')
<div class="max-w-xl mx-auto">

    {{-- Header card --}}
    <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-3xl p-6 mb-6 text-white shadow-lg">
        <div class="flex items-center gap-4 mb-3">
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center flex-shrink-0">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold">Download Your eNID</h1>
                <p class="text-blue-200 text-sm">Official DONIDCR Citizen Portal</p>
            </div>
        </div>
        <p class="text-blue-100 text-sm leading-relaxed">
            Enter your citizenship details below. DONIDCR will send an OTP to your
            registered mobile number. Once verified, your eNID PDF will download automatically.
        </p>
    </div>

    {{-- Steps indicator --}}
    <div class="flex items-center gap-0 mb-6" id="steps-bar">
        @foreach([['1','Details & CAPTCHA'],['2','Enter OTP'],['3','Download']] as $i => [$num,$label])
        <div class="flex-1 flex flex-col items-center">
            <div id="step-dot-{{ $num }}"
                 class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-all
                        {{ $i === 0 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                {{ $num }}
            </div>
            <p id="step-label-{{ $num }}"
               class="text-xs mt-1 font-medium transition-all
                      {{ $i === 0 ? 'text-blue-600' : 'text-gray-400' }}">
                {{ $label }}
            </p>
        </div>
        @if($i < 2)
        <div class="flex-1 h-0.5 bg-gray-200 -mt-4" id="step-line-{{ $num }}"></div>
        @endif
        @endforeach
    </div>

    {{-- STEP 1: Details + CAPTCHA --}}
    <div id="panel-step1" class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-4">
        <h2 class="font-bold text-gray-900 text-base">Step 1 — Your Details</h2>

        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Full Name (English) <span class="text-red-500">*</span>
                </label>
                <input id="inp-full-name" type="text" autocomplete="name"
                       placeholder="e.g. RAM KUMAR SHRESTHA"
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 uppercase">
                <p class="text-[11px] text-gray-400 mt-1">As on your Citizenship Certificate (capital letters)</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Full Name (Nepali) <span class="text-red-500">*</span>
                </label>
                {{-- Nepali input with Nepalify transliteration (same as govt site) --}}
                <div class="relative">
                    <input id="inp-full-name-np" type="text"
                           placeholder="यहाँ नेपालीमा टाइप गर्नुहोस् (राम कुमार श्रेष्ठ)"
                           class="nepalify w-full rounded-xl border border-gray-200 px-4 py-2.5 pr-20 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                           style="font-family:'Noto Sans Devanagari',sans-serif;">
                    <button type="button" id="btn-toggle-np"
                            onclick="toggleNepalify()"
                            class="absolute right-2 top-1/2 -translate-y-1/2 px-2 py-1 text-[11px] font-bold rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition border border-blue-200">
                        नेपाली
                    </button>
                </div>
                <p class="text-[11px] text-gray-400 mt-1">
                    Roman type गर्नुहोस् — automatically converts to नेपाली &nbsp;•&nbsp;
                    e.g. type <kbd class="bg-gray-100 px-1 rounded text-[10px]">ra</kbd> → <span class="font-semibold">र</span>,
                    <kbd class="bg-gray-100 px-1 rounded text-[10px]">ma</kbd> → <span class="font-semibold">म</span>
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Date of Birth (BS) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input id="inp-dob" type="text"
                           placeholder="२०५०-०१-०१"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 font-mono"
                           style="font-family:'Noto Sans Devanagari',monospace;">
                    <button type="button" onclick="toggleNepaliDigits('inp-dob')"
                            class="absolute right-2 top-1/2 -translate-y-1/2 px-2 py-1 text-[11px] font-bold rounded-lg bg-orange-100 text-orange-700 hover:bg-orange-200 transition border border-orange-200">
                        अंक
                    </button>
                </div>
                <p class="text-[11px] text-gray-400 mt-1">Bikram Sambat (BS)</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Citizenship Date (BS) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input id="inp-cit-date" type="text"
                           placeholder="२०७०-०१-०१"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 font-mono"
                           style="font-family:'Noto Sans Devanagari',monospace;">
                    <button type="button" onclick="toggleNepaliDigits('inp-cit-date')"
                            class="absolute right-2 top-1/2 -translate-y-1/2 px-2 py-1 text-[11px] font-bold rounded-lg bg-orange-100 text-orange-700 hover:bg-orange-200 transition border border-orange-200">
                        अंक
                    </button>
                </div>
                <p class="text-[11px] text-gray-400 mt-1">Issuing date (BS)</p>
            </div>
            </div>
        </div>

        {{-- CAPTCHA --}}
        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
            <label class="block text-xs font-semibold text-gray-700 mb-2">
                CAPTCHA <span class="text-red-500">*</span>
            </label>
            <div class="flex items-center gap-3 mb-3">
                <div id="captcha-img-wrap"
                     class="flex-1 bg-white border border-gray-200 rounded-xl flex items-center justify-center h-14 overflow-hidden">
                    <span id="captcha-loading" class="text-xs text-gray-400 flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin text-blue-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                        Loading…
                    </span>
                    <img id="captcha-img" src="" alt="CAPTCHA" class="hidden h-12 object-contain">
                </div>
                <button type="button" onclick="loadCaptcha()"
                        id="btn-refresh-captcha"
                        class="p-2.5 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
            </div>
            <input id="inp-captcha" type="text" maxlength="6" inputmode="numeric"
                   placeholder="Type the 6 digits shown above"
                   class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 font-mono tracking-widest text-center">
        </div>

        <div id="step1-error" class="hidden"></div>

        <button onclick="submitStep1()"
                id="btn-step1"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition shadow-sm flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 18h.01M8 21h8a2 2 0 002-2v-1a7 7 0 00-14 0v1a2 2 0 002 2z"/>
            </svg>
            Send OTP to My Phone
        </button>

        <p class="text-center text-xs text-gray-400">
            An OTP will be sent to the mobile number registered with DONIDCR.
        </p>
    </div>

    {{-- STEP 2: OTP Entry --}}
    <div id="panel-step2" class="hidden bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-5">
        <div class="text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 18h.01M8 21h8a2 2 0 002-2v-1a7 7 0 00-14 0v1a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="font-bold text-gray-900 text-base">Enter OTP</h2>
            <p class="text-sm text-gray-500 mt-1">
                DONIDCR sent a 6-digit OTP to your registered mobile.
            </p>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-2 text-center">OTP Code</label>
            <input id="inp-otp" type="text" maxlength="6" inputmode="numeric"
                   placeholder="• • • • • •"
                   class="w-full text-center rounded-xl border-2 border-gray-200 focus:border-blue-400 px-4 py-3 text-2xl font-mono font-bold tracking-[0.5em] focus:outline-none transition">
            <div id="otp-timer" class="text-center text-xs text-gray-400 mt-2">
                OTP valid for <span id="timer-count" class="font-bold text-blue-600">60</span>s
            </div>
        </div>

        <div id="step2-error" class="hidden"></div>

        <button onclick="submitStep2()" id="btn-step2"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl transition shadow-sm flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Verify OTP & Download eNID
        </button>

        <div class="flex items-center justify-between text-xs text-gray-500">
            <button onclick="goBack()" class="hover:text-blue-600 transition">← Back</button>
            <button id="btn-resend" onclick="resendOtp()" disabled
                    class="text-blue-600 hover:underline disabled:opacity-40 disabled:no-underline transition">
                Resend OTP (<span id="resend-count">60</span>s)
            </button>
        </div>
    </div>

    {{-- STEP 3: Success --}}
    <div id="panel-step3" class="hidden bg-white rounded-3xl border border-gray-100 shadow-sm p-8 text-center space-y-4">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h2 class="font-bold text-gray-900 text-xl">eNID Downloaded!</h2>
        <p class="text-gray-500 text-sm">Your National ID Card PDF has been downloaded. Check your downloads folder.</p>
        <div id="nin-display" class="hidden bg-blue-50 border border-blue-200 rounded-2xl p-4">
            <p class="text-xs text-blue-600 font-semibold uppercase tracking-wider mb-1">Your NIN</p>
            <p id="nin-value" class="text-2xl font-mono font-bold text-blue-800 tracking-widest"></p>
        </div>
        <button onclick="resetForm()"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition shadow-sm">
            Download Again
        </button>
    </div>

</div>
@endsection

@section('scripts')
{{-- Nepalify — same library used by the official DONIDCR Citizen Portal --}}
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/nepalify@0.5.0/dist/nepalify.min.js"></script>
{{-- Noto Sans Devanagari for proper rendering --}}
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700&display=swap" rel="stylesheet">
<script>
// ── Nepalify toggle (EN ↔ नेपाली) ────────────────────────────────────────
let nepalifyOn = true;

function toggleNepalify() {
    nepalifyOn = !nepalifyOn;
    nepalify.toggle();
    const btn = document.getElementById('btn-toggle-np');
    if (nepalifyOn) {
        btn.textContent = 'नेपाली';
        btn.className = 'absolute right-2 top-1/2 -translate-y-1/2 px-2 py-1 text-[11px] font-bold rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition border border-blue-200';
    } else {
        btn.textContent = 'EN';
        btn.className = 'absolute right-2 top-1/2 -translate-y-1/2 px-2 py-1 text-[11px] font-bold rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition border border-gray-200';
    }
}

// ── Date input auto-formatter (exact DONIDCR algorithm) ──────────────────
// L = digit map (Arabic → Nepali). Same as govt site's L constant.
const L = {'0':'०','1':'१','2':'२','3':'३','4':'४','5':'५','6':'६','7':'७','8':'८','9':'९'};
const L_keys   = Object.keys(L);    // ['0','1',...,'9']
const L_values = Object.values(L);  // ['०','१',...,'९']

function formatNepaliDate(rawValue) {
    // Step 1: keep only valid digit chars (Nepali or Arabic), convert Arabic→Nepali
    let s = Array.from(rawValue)
        .map(ch => {
            if (L_values.includes(ch)) return ch;        // already Nepali digit
            if (L_keys.includes(ch))   return L[ch];     // Arabic → Nepali
            return '';                                    // drop everything else
        })
        .join('');

    // Step 2: auto-insert dashes  YYYY-MM-DD
    if (s.length > 4) s = s.slice(0, 4) + '-' + s.slice(4);
    if (s.length > 7) s = s.slice(0, 7) + '-' + s.slice(7);
    if (s.length > 10) s = s.slice(0, 10);   // cap at YYYY-MM-DD

    return s;
}

function setupDateInput(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('input', function () {
        const pos  = this.selectionStart;
        const newVal = formatNepaliDate(this.value);
        if (newVal !== this.value) {
            this.value = newVal;
            // keep cursor in sensible position
            const newPos = Math.min(pos, newVal.length);
            this.setSelectionRange(newPos, newPos);
        }
    });
}

// Toggle Nepali ↔ Arabic digits (the अंक button)
function toggleNepaliDigits(id) {
    const el = document.getElementById(id);
    if (!el) return;
    const hasNepali = L_values.some(d => el.value.includes(d));
    // Convert all digits the other way, keep dashes
    el.value = Array.from(el.value).map(ch => {
        if (hasNepali) { // Nepali → Arabic
            const i = L_values.indexOf(ch);
            return i !== -1 ? L_keys[i] : ch;
        } else {          // Arabic → Nepali
            return L[ch] ?? ch;
        }
    }).join('');
    el.focus();
}

// ── State ────────────────────────────────────────────────────────────────
let transactionId = null;
let timerInterval = null;
let timerSeconds  = 60;

// ── CSRF helper — reads the meta tag on every call so it's always fresh ──
function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function apiHeaders(extra = {}) {
    return {
        'Content-Type':     'application/json',
        'Accept':           'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-XSRF-TOKEN':     decodeURIComponent(
            document.cookie.split('; ')
                .find(r => r.startsWith('XSRF-TOKEN='))
                ?.split('=')[1] ?? ''
        ),
        ...extra,
    };
}

// ── Step indicators ───────────────────────────────────────────────────────
function setStep(n) {
    [1,2,3].forEach(i => {
        const dot   = document.getElementById('step-dot-'+i);
        const label = document.getElementById('step-label-'+i);
        const panel = document.getElementById('panel-step'+i);
        if (i < n) {
            dot.className   = 'w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-all bg-green-500 text-white';
            dot.innerHTML   = '✓';
            label.className = 'text-xs mt-1 font-medium transition-all text-green-600';
        } else if (i === n) {
            dot.className   = 'w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-all bg-blue-600 text-white';
            dot.innerHTML   = String(i);
            label.className = 'text-xs mt-1 font-medium transition-all text-blue-600';
        } else {
            dot.className   = 'w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-all bg-gray-200 text-gray-500';
            dot.innerHTML   = String(i);
            label.className = 'text-xs mt-1 font-medium transition-all text-gray-400';
        }
        if (panel) panel.classList.toggle('hidden', i !== n);
    });
}

// ── CAPTCHA ───────────────────────────────────────────────────────────────
async function loadCaptcha() {
    const img     = document.getElementById('captcha-img');
    const loading = document.getElementById('captcha-loading');
    const btn     = document.getElementById('btn-refresh-captcha');
    img.classList.add('hidden');
    loading.classList.remove('hidden');
    btn.disabled = true;

    try {
        const res  = await fetch('/api/v1/nid/captcha', { credentials: 'same-origin' });
        const json = await res.json();
        if (json.success && json.data?.image) {
            img.src = 'data:image/png;base64,' + json.data.image;
            img.onload = () => {
                loading.classList.add('hidden');
                img.classList.remove('hidden');
            };
        } else {
            loading.innerHTML = '<span class="text-red-500 text-xs">Failed — click ↻ to retry</span>';
        }
    } catch {
        loading.innerHTML = '<span class="text-red-500 text-xs">Network error — click ↻ to retry</span>';
    } finally {
        btn.disabled = false;
    }
}

// ── Step 1: Submit details + CAPTCHA ─────────────────────────────────────
async function submitStep1() {
    const fullName  = document.getElementById('inp-full-name').value.trim().toUpperCase();
    const fullNamNp = document.getElementById('inp-full-name-np').value.trim();
    const dob       = document.getElementById('inp-dob').value.trim();
    const citDate   = document.getElementById('inp-cit-date').value.trim();
    const captcha   = document.getElementById('inp-captcha').value.trim();

    clearError('step1-error');

    if (!fullName)  return showError('step1-error', 'Full name (English) is required.');
    if (!fullNamNp) return showError('step1-error', 'Full name (Nepali) is required.');
    if (!dob)       return showError('step1-error', 'Date of birth is required.');
    if (!citDate)   return showError('step1-error', 'Citizenship issuing date is required.');
    if (captcha.length !== 6) return showError('step1-error', 'Enter the 6-digit CAPTCHA.');

    const btn = document.getElementById('btn-step1');
    setBtnLoading(btn, 'Sending OTP…');

    try {
        const res  = await fetch('/api/v1/nid/request-otp', {
            method:  'POST',
            headers: apiHeaders(),
            credentials: 'same-origin',
            body: JSON.stringify({
                full_name:             fullName,
                full_name_np:          fullNamNp,
                dob_bs:                dob,
                citizenship_issued_bs: citDate,
                captcha:               captcha,
            }),
        });
        const json = await res.json();

        if (json.error === 'invalid_captcha') {
            showError('step1-error', 'Invalid CAPTCHA. Please refresh the image and try again.');
            loadCaptcha();
            document.getElementById('inp-captcha').value = '';
            return;
        }
        if (!json.success) {
            showError('step1-error', json.message ?? 'Failed to send OTP. Check your details.');
            loadCaptcha();
            return;
        }

        transactionId = json.data?.transaction_id;
        setStep(2);
        startTimer();
    } catch {
        showError('step1-error', 'Network error. Please try again.');
    } finally {
        setBtnNormal(btn, `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 18h.01M8 21h8a2 2 0 002-2v-1a7 7 0 00-14 0v1a2 2 0 002 2z"/>
            </svg>
            Send OTP to My Phone`);
    }
}

// ── Step 2: Verify OTP → download ────────────────────────────────────────
async function submitStep2() {
    const otp = document.getElementById('inp-otp').value.trim();
    clearError('step2-error');
    if (otp.length !== 6) return showError('step2-error', 'Enter the 6-digit OTP.');

    const btn = document.getElementById('btn-step2');
    setBtnLoading(btn, 'Verifying & Downloading…');

    try {
        const res = await fetch('/api/v1/nid/download', {
            method:  'POST',
            headers: apiHeaders({'Accept': 'application/json, application/pdf'}),
            credentials: 'same-origin',
            body: JSON.stringify({
                transaction_id:        transactionId,
                otp:                   otp,
                full_name:             document.getElementById('inp-full-name').value.trim().toUpperCase(),
                full_name_np:          document.getElementById('inp-full-name-np').value.trim(),
                dob_bs:                document.getElementById('inp-dob').value.trim(),
                citizenship_issued_bs: document.getElementById('inp-cit-date').value.trim(),
            }),
        });

        // If JSON error response
        const contentType = res.headers.get('Content-Type') ?? '';
        if (contentType.includes('application/json')) {
            const json = await res.json();
            showError('step2-error', json.message ?? 'Download failed.');
            return;
        }

        // PDF blob — trigger browser download
        if (!res.ok) {
            showError('step2-error', 'Download failed. Please try again.');
            return;
        }

        const nin    = res.headers.get('X-NIN') ?? '';
        const blob   = await res.blob();
        const url    = window.URL.createObjectURL(blob);
        const a      = document.createElement('a');
        a.href       = url;
        a.download   = 'eNID_' + Date.now() + '.pdf';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        a.remove();

        clearInterval(timerInterval);
        setStep(3);

        if (nin) {
            document.getElementById('nin-value').textContent = nin;
            document.getElementById('nin-display').classList.remove('hidden');
        }
    } catch {
        showError('step2-error', 'Network error. Please try again.');
    } finally {
        setBtnNormal(btn, `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Verify OTP & Download eNID`);
    }
}

// ── Resend OTP ────────────────────────────────────────────────────────────
async function resendOtp() {
    const btn = document.getElementById('btn-resend');
    btn.disabled = true;
    clearError('step2-error');

    try {
        const res  = await fetch('/api/v1/nid/request-otp', {
            method:  'POST',
            headers: apiHeaders(),
            credentials: 'same-origin',
            body: JSON.stringify({
                full_name:             document.getElementById('inp-full-name').value.trim().toUpperCase(),
                full_name_np:          document.getElementById('inp-full-name-np').value.trim(),
                dob_bs:                document.getElementById('inp-dob').value.trim(),
                citizenship_issued_bs: document.getElementById('inp-cit-date').value.trim(),
                captcha:               '000000',   // resend uses existing session — captcha skipped by DONIDCR
            }),
        });
        const json = await res.json();
        if (json.success) {
            transactionId = json.data?.transaction_id ?? transactionId;
            startTimer();
        } else {
            showError('step2-error', json.message ?? 'Resend failed.');
        }
    } catch {
        showError('step2-error', 'Resend failed. Try again.');
        btn.disabled = false;
    }
}

// ── Timer ─────────────────────────────────────────────────────────────────
function startTimer() {
    clearInterval(timerInterval);
    timerSeconds = 60;
    document.getElementById('btn-resend').disabled = true;
    tick();
    timerInterval = setInterval(tick, 1000);
}

function tick() {
    timerSeconds--;
    const tc = document.getElementById('timer-count');
    const rc = document.getElementById('resend-count');
    const rb = document.getElementById('btn-resend');
    if (tc) tc.textContent = Math.max(0, timerSeconds);
    if (rc) rc.textContent = Math.max(0, timerSeconds);
    if (timerSeconds <= 0) {
        clearInterval(timerInterval);
        if (rb) { rb.disabled = false; rb.textContent = 'Resend OTP'; }
        if (tc) tc.closest('#otp-timer').innerHTML = '<span class="text-orange-500 font-semibold">OTP expired</span>';
    }
}

// ── Navigation ────────────────────────────────────────────────────────────
function goBack() {
    clearInterval(timerInterval);
    setStep(1);
    loadCaptcha();
    document.getElementById('inp-captcha').value = '';
    document.getElementById('inp-otp').value     = '';
    clearError('step1-error');
    clearError('step2-error');
}

function resetForm() {
    transactionId = null;
    ['inp-full-name','inp-full-name-np','inp-dob','inp-cit-date','inp-captcha','inp-otp']
        .forEach(id => { const el = document.getElementById(id); if(el) el.value = ''; });
    setStep(1);
    loadCaptcha();
}

// ── Helpers ───────────────────────────────────────────────────────────────
function showError(id, msg) {
    const el = document.getElementById(id);
    if (!el) return;
    el.className = 'flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 rounded-xl p-3 text-xs font-medium';
    el.innerHTML = `<svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>${msg}</span>`;
}

function clearError(id) {
    const el = document.getElementById(id);
    if (el) { el.className = 'hidden'; el.innerHTML = ''; }
}

function setBtnLoading(btn, text) {
    btn.disabled = true;
    btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> ${text}`;
}

function setBtnNormal(btn, html) {
    btn.disabled = false;
    btn.innerHTML = html;
}

// ── Init ──────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    setStep(1);
    loadCaptcha();

    // Auto uppercase for English name
    document.getElementById('inp-full-name')
        ?.addEventListener('input', e => e.target.value = e.target.value.toUpperCase());

    // Numeric only for OTP
    document.getElementById('inp-otp')
        ?.addEventListener('input', e => e.target.value = e.target.value.replace(/[^0-9]/g,''));

    // Date fields: type 2050-01-01 → auto converts to २०५०-०१-०१
    setupDateInput('inp-dob');
    setupDateInput('inp-cit-date');

    // Init nepalify on the Nepali name field (same class the govt site uses)
    // nepalify.js auto-attaches to elements with class="nepalify"
    if (typeof nepalify !== 'undefined') {
        nepalify.setNepalifyClass('nepalify');
    }
});
</script>
@endsection
