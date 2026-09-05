@extends('admin.layouts.app')

@section('title', 'Document Verification Logs')
@section('subtitle', 'Audit trail of all government document verification attempts')

@section('content')

{{-- Status banner --}}
<div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-2xl p-4 flex items-start gap-3">
    <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <div>
        <p class="font-semibold text-yellow-800 text-sm">All verification providers are pending official authorization</p>
        <p class="text-yellow-700 text-xs mt-1">
            NID (DONIDCR), Driving Licence (DoTM), PAN (IRD), and Citizenship (MoHA) all require
            an official API agreement before live verification can be activated.
            Contact the relevant government authority for an authorized integration MOU.
        </p>
    </div>
</div>

{{-- Provider status cards --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['NID', 'DONIDCR', 'nid', '#1565C0'],
        ['Licence', 'DoTM', 'licence', '#2E7D32'],
        ['PAN', 'IRD', 'pan', '#E65100'],
        ['Citizenship', 'MoHA', 'citizenship', '#6A1B9A'],
    ] as [$label, $provider, $type, $color])
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $label }}</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                Pending Auth
            </span>
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['by_type'][$type] ?? 0 }}</p>
        <p class="text-xs text-gray-500 mt-1">total attempts · {{ $provider }}</p>
    </div>
    @endforeach
</div>

{{-- Today stats --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-[#E1E8E1] rounded-2xl p-5 shadow-sm">
        <p class="text-3xl font-bold text-[#334033]">{{ number_format($stats['total_today']) }}</p>
        <p class="text-sm text-[#4A5D4A] mt-1">Attempts today</p>
    </div>
    <div class="bg-[#E1E8E1] rounded-2xl p-5 shadow-sm">
        <p class="text-3xl font-bold text-[#334033]">{{ number_format($stats['verified_today']) }}</p>
        <p class="text-sm text-[#4A5D4A] mt-1">Verified today</p>
    </div>
    <div class="bg-[#E8DFE1] rounded-2xl p-5 shadow-sm">
        <p class="text-3xl font-bold text-[#403333]">{{ number_format($stats['pending_auth']) }}</p>
        <p class="text-sm text-[#5D4A4A] mt-1">Pending authorization (all time)</p>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-3 mb-6">
    <select name="document_type"
            class="rounded-xl border border-gray-200 text-sm px-3 py-2 focus:ring-2 focus:ring-[#4A5D4A] bg-white">
        <option value="">All document types</option>
        <option value="nid"         {{ request('document_type') === 'nid'         ? 'selected' : '' }}>NID</option>
        <option value="licence"     {{ request('document_type') === 'licence'     ? 'selected' : '' }}>Driving Licence</option>
        <option value="pan"         {{ request('document_type') === 'pan'         ? 'selected' : '' }}>PAN</option>
        <option value="citizenship" {{ request('document_type') === 'citizenship' ? 'selected' : '' }}>Citizenship</option>
    </select>

    <select name="status"
            class="rounded-xl border border-gray-200 text-sm px-3 py-2 focus:ring-2 focus:ring-[#4A5D4A] bg-white">
        <option value="">All statuses</option>
        <option value="pending_authorization" {{ request('status') === 'pending_authorization' ? 'selected' : '' }}>Pending Authorization</option>
        <option value="verified"              {{ request('status') === 'verified'              ? 'selected' : '' }}>Verified</option>
        <option value="not_found"             {{ request('status') === 'not_found'             ? 'selected' : '' }}>Not Found</option>
        <option value="error"                 {{ request('status') === 'error'                 ? 'selected' : '' }}>Error / Rate Limited</option>
        <option value="provider_unavailable"  {{ request('status') === 'provider_unavailable'  ? 'selected' : '' }}>Provider Unavailable</option>
    </select>

    <input type="date" name="date" value="{{ request('date') }}"
           class="rounded-xl border border-gray-200 text-sm px-3 py-2 focus:ring-2 focus:ring-[#4A5D4A] bg-white">

    <button type="submit"
            class="px-4 py-2 bg-[#4A5D4A] text-white rounded-xl text-sm font-medium hover:bg-[#3F523F] transition">
        Filter
    </button>
    @if(request()->hasAny(['document_type','status','date']))
    <a href="{{ route('admin.verification.audit-logs') }}"
       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-300 transition">
        Clear
    </a>
    @endif
</form>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-semibold text-gray-800">Audit Log</h2>
        <p class="text-xs text-gray-400">
            Raw document numbers are never stored — only SHA-256 hashes for abuse detection.
        </p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doc Hash (first 16)</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-gray-500 whitespace-nowrap text-xs">
                        {{ $log->created_at->format('d M H:i') }}
                    </td>
                    <td class="px-4 py-3">
                        @if($log->user)
                            <p class="font-medium text-gray-800 text-xs">{{ $log->user->name }}</p>
                            <p class="text-gray-400 text-xs">{{ $log->user->email }}</p>
                        @else
                            <span class="text-gray-400 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $typeColors = [
                                'nid'         => 'bg-blue-100 text-blue-800',
                                'licence'     => 'bg-green-100 text-green-800',
                                'pan'         => 'bg-orange-100 text-orange-800',
                                'citizenship' => 'bg-purple-100 text-purple-800',
                            ];
                        @endphp
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$log->document_type] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ strtoupper($log->document_type) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600 text-xs max-w-[160px] truncate" title="{{ $log->provider }}">
                        {{ $log->provider }}
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $statusColors = [
                                'verified'              => 'bg-green-100 text-green-800',
                                'pending_authorization' => 'bg-yellow-100 text-yellow-800',
                                'not_found'             => 'bg-gray-100 text-gray-700',
                                'mismatch'              => 'bg-red-100 text-red-800',
                                'error'                 => 'bg-red-100 text-red-800',
                                'provider_unavailable'  => 'bg-gray-100 text-gray-500',
                            ];
                        @endphp
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$log->status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ str_replace('_', ' ', $log->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 font-mono text-xs text-gray-400">
                        {{ $log->document_hash ? substr($log->document_hash, 0, 16) . '…' : '—' }}
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $log->ip_address ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">
                        {{ $log->duration_ms ? $log->duration_ms . 'ms' : '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-400 text-sm">
                        No verification attempts found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $logs->links() }}
    </div>
    @endif
</div>

@endsection
