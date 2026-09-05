@extends('layouts.user')

@section('title', 'Verify Documents')
@section('subtitle', 'Check the official status of your government-issued documents')

@section('content')

{{-- Info banner --}}
<div class="mb-6 bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-start gap-3">
    <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div>
        <p class="font-semibold text-amber-800 text-sm">Official integrations are in progress</p>
        <p class="text-amber-700 text-xs mt-1 leading-relaxed">
            We are in the process of obtaining official API agreements from DONIDCR, DoTM, and IRD.
            Once authorized, you will be able to verify your NID, Driving Licence, PAN, and Citizenship
            directly from this page. The forms below are ready — verification will activate automatically
            when each government authority grants access.
        </p>
    </div>
</div>

{{-- Provider status row --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @foreach($providers as $p)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col gap-2">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $p['label'] }}</span>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold
                {{ $p['available'] ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $p['available'] ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                {{ $p['available'] ? 'Live' : 'Pending' }}
            </span>
        </div>
        <p class="text-[11px] text-gray-500 leading-relaxed">{{ $p['provider_short'] }}</p>
    </div>
    @endforeach
</div>

{{-- Verification cards --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- NID card --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-blue-100">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-900 text-sm">National Identity Number (NIN)</h3>
                <p class="text-xs text-gray-500">DONIDCR · राष्ट्रिय परिचयपत्र</p>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span>Pending Auth
            </span>
        </div>
        <form id="form-nid" class="p-6 space-y-4" onsubmit="submitVerification(event,'nid')">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">NIN</label>
                <input type="text" name="nin" maxlength="12" inputmode="numeric"
                       placeholder="e.g. 123-456-789-0"
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 font-mono tracking-widest">
                <p class="text-[11px] text-gray-400 mt-1">Format: XXX-XXX-XXX-X — as printed on your National ID card</p>
            </div>
            <div id="result-nid"></div>
            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 disabled:opacity-60"
                    id="btn-nid">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Verify NIN
            </button>
        </form>
    </div>

    {{-- Driving Licence card --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-green-100">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l1.293-1.293A1 1 0 015 14h1m7 2l1-4h4.5a.5.5 0 00.447-.276l1-2A.5.5 0 0019.5 9H14"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-900 text-sm">Driving Licence</h3>
                <p class="text-xs text-gray-500">DoTM · सवारी चालक अनुमतिपत्र</p>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span>Pending Auth
            </span>
        </div>
        <form id="form-licence" class="p-6 space-y-4" onsubmit="submitVerification(event,'licence')">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Licence Number</label>
                <input type="text" name="licence_number" maxlength="14"
                       placeholder="e.g. 01-01-12345678"
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 font-mono tracking-widest">
                <p class="text-[11px] text-gray-400 mt-1">Format: XX-XX-XXXXXXXX (e.g. 01-01-12345678)</p>
            </div>
            <div id="result-licence"></div>
            <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 disabled:opacity-60"
                    id="btn-licence">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Verify Licence
            </button>
        </form>
    </div>


    {{-- PAN card --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-orange-100">
            <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-900 text-sm">PAN Number</h3>
                <p class="text-xs text-gray-500">IRD · स्थायी लेखा नम्बर</p>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span>Pending Auth
            </span>
        </div>
        <form id="form-pan" class="p-6 space-y-4" onsubmit="submitVerification(event,'pan')">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">PAN (9 digits)</label>
                <input type="text" name="pan" maxlength="9" inputmode="numeric"
                       pattern="\d{9}"
                       placeholder="e.g. 123456789"
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 font-mono tracking-widest">
                <p class="text-[11px] text-gray-400 mt-1">Your 9-digit Permanent Account Number</p>
            </div>
            <div id="result-pan"></div>
            <button type="submit"
                    class="w-full bg-orange-600 hover:bg-orange-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 disabled:opacity-60"
                    id="btn-pan">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Verify PAN
            </button>
        </form>
    </div>

    {{-- Citizenship card --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-purple-100">
            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-900 text-sm">Citizenship Certificate</h3>
                <p class="text-xs text-gray-500">MoHA · नागरिकता प्रमाणपत्र</p>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span>Pending Auth
            </span>
        </div>
        <form id="form-citizenship" class="p-6 space-y-4" onsubmit="submitVerification(event,'citizenship')">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Citizenship Number</label>
                <input type="text" name="citizenship_number" maxlength="30"
                       placeholder="e.g. 01-075-12345"
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 font-mono tracking-widest">
                <p class="text-[11px] text-gray-400 mt-1">Format: District-Year-Number (e.g. 01-075-12345)</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Issued District <span class="text-gray-400 font-normal">(optional)</span></label>
                <input type="text" name="issued_district"
                       placeholder="e.g. Kathmandu"
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
            </div>
            <div id="result-citizenship"></div>
            <button type="submit"
                    class="w-full bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 disabled:opacity-60"
                    id="btn-citizenship">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Verify Citizenship
            </button>
        </form>
    </div>

</div>{{-- end grid --}}

{{-- My verification history --}}
<div class="mt-8 bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h2 class="font-bold text-gray-900">My Verification History</h2>
        <span class="text-xs text-gray-400">Raw document numbers are never stored</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">When</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Result</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($history as $log)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3 text-gray-500 text-xs whitespace-nowrap">
                        {{ $log->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="px-6 py-3">
                        @php
                            $typeColors = [
                                'nid'         => 'bg-blue-100 text-blue-800',
                                'licence'     => 'bg-green-100 text-green-800',
                                'pan'         => 'bg-orange-100 text-orange-800',
                                'citizenship' => 'bg-purple-100 text-purple-800',
                            ];
                        @endphp
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $typeColors[$log->document_type] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ strtoupper($log->document_type) }}
                        </span>
                    </td>
                    <td class="px-6 py-3">
                        @if($log->verified)
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Verified
                            </span>
                        @else
                            <span class="text-xs text-gray-500">{{ str_replace('_', ' ', $log->status) }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-10 text-center text-gray-400 text-sm">
                        No verification attempts yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
const API_TOKEN = @json(auth()->user()->tokens()->latest()->value('id')
    ? auth()->user()->currentAccessToken()?->token
    : null);

// Get a fresh Sanctum token for web → API calls via a hidden meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

// Auto-format NIN as XXX-XXX-XXX-X while typing
document.querySelector('input[name="nin"]')?.addEventListener('input', function (e) {
    let raw = e.target.value.replace(/[^0-9]/g, '').slice(0, 10);
    let formatted = '';
    if (raw.length > 9)      formatted = raw.slice(0,3)+'-'+raw.slice(3,6)+'-'+raw.slice(6,9)+'-'+raw.slice(9,10);
    else if (raw.length > 6) formatted = raw.slice(0,3)+'-'+raw.slice(3,6)+'-'+raw.slice(6);
    else if (raw.length > 3) formatted = raw.slice(0,3)+'-'+raw.slice(3);
    else                     formatted = raw;
    e.target.value = formatted;
});

async function submitVerification(e, type) {
    e.preventDefault();

    const form    = document.getElementById('form-' + type);
    const btn     = document.getElementById('btn-' + type);
    const resultEl = document.getElementById('result-' + type);
    const data    = Object.fromEntries(new FormData(form));

    // Remove the Laravel CSRF field — it's not needed for the API
    delete data['_token'];

    btn.disabled = true;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Verifying…';
    resultEl.innerHTML = '';

    try {
        const res  = await fetch('/api/v1/verification/' + type, {
            method:  'POST',
            headers: {
                'Content-Type':  'application/json',
                'Accept':        'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                // Web portal uses cookie-based session auth via Sanctum SPA
            },
            credentials: 'same-origin',
            body: JSON.stringify(data),
        });

        const json = await res.json();
        const status = json.data?.status ?? 'unknown';
        const message = json.data?.message ?? 'Unknown response.';

        if (res.status === 401) {
            showResult(resultEl, 'error', 'Please log in to verify documents.');
        } else if (status === 'verified') {
            showResult(resultEl, 'success', '✓ Document verified successfully.');
        } else if (status === 'pending_authorization') {
            showResult(resultEl, 'pending',
                'This verification service is pending official government API authorization. ' +
                'We will notify you when it becomes available.');
        } else if (status === 'not_found') {
            showResult(resultEl, 'error', 'Document not found in government records.');
        } else if (res.status === 429) {
            showResult(resultEl, 'error', 'Too many attempts. Please wait before trying again.');
        } else if (res.status === 422) {
            const errors = Object.values(json.errors ?? {}).flat();
            showResult(resultEl, 'error', errors[0] ?? 'Please check your input.');
        } else {
            showResult(resultEl, 'pending', message);
        }
    } catch (err) {
        showResult(resultEl, 'error', 'Network error. Please try again.');
    } finally {
        const labels = { nid: 'NIN', licence: 'Licence', pan: 'PAN', citizenship: 'Citizenship' };
        btn.disabled = false;
        btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg> Verify ${labels[type]}`;
    }
}

function showResult(el, type, message) {
    const styles = {
        success: 'bg-green-50 border-green-200 text-green-800',
        error:   'bg-red-50 border-red-200 text-red-800',
        pending: 'bg-yellow-50 border-yellow-200 text-yellow-800',
    };
    const icons = {
        success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
        error:   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
        pending: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    };
    el.innerHTML = `
        <div class="flex items-start gap-2.5 rounded-xl border p-3 text-xs font-medium ${styles[type]}">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${icons[type]}
            </svg>
            <span>${message}</span>
        </div>`;
}
</script>
@endsection
