<div>
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Locker Documents</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="w-14 h-14 bg-[#4A5D4A] rounded-xl flex items-center justify-center shadow-inner">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Expiring Soon (30 Days)</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $stats['expiring_soon'] }}</p>
                </div>
                <div class="w-14 h-14 bg-yellow-100 rounded-xl flex items-center justify-center shadow-inner">
                    <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Expired Documents</p>
                    <p class="text-3xl font-bold text-red-600 mt-1">{{ $stats['expired'] }}</p>
                </div>
                <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center shadow-inner">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="flex items-center justify-between border-b border-gray-200 mb-6 pb-2">
        <div class="flex items-center gap-4">
            <button wire:click="setTab('documents')"
                    class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-all flex items-center gap-2 {{ $activeTab === 'documents' ? 'bg-[#4A5D4A] text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                </svg>
                Digital Locker Documents
                <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $activeTab === 'documents' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700' }}">{{ $stats['total'] }}</span>
            </button>

            <button wire:click="setTab('history')"
                    class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-all flex items-center gap-2 {{ $activeTab === 'history' ? 'bg-[#4A5D4A] text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Locker Activity History
            </button>
        </div>

        @if($activeTab === 'documents')
        <button wire:click="resetForm"
                class="bg-[#4A5D4A] hover:bg-[#3A4D3A] text-white px-5 py-2.5 rounded-xl font-medium transition-all flex items-center gap-2 shadow-sm text-sm"
                data-bs-toggle="modal" data-bs-target="#documentModal">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Document
        </button>
        @endif
    </div>

    @if($activeTab === 'documents')
    {{-- Filters --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live="search" type="text" placeholder="Search documents by title or document number..."
                       class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#4A5D4A] focus:border-[#4A5D4A] outline-none transition-all text-sm">
            </div>
            <select wire:model.live="typeFilter" class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#4A5D4A] focus:border-[#4A5D4A] outline-none transition-all text-sm">
                <option value="">All Document Types</option>
                @foreach($types as $typeKey => $typeLabel)
                    <option value="{{ $typeKey }}">{{ $typeLabel }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Documents Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-[#E1E8E1]">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#4A5D4A] uppercase tracking-wider cursor-pointer hover:bg-[#D8E0D8] transition-colors"
                            wire:click="sort('title')">
                            <div class="flex items-center gap-1">
                                Document Title
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                </svg>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#4A5D4A] uppercase tracking-wider cursor-pointer hover:bg-[#D8E0D8] transition-colors"
                            wire:click="sort('type')">
                            <div class="flex items-center gap-1">
                                Type
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                </svg>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#4A5D4A] uppercase tracking-wider cursor-pointer hover:bg-[#D8E0D8] transition-colors"
                            wire:click="sort('expiry_date')">
                            <div class="flex items-center gap-1">
                                Expiry & Validity
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                </svg>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#4A5D4A] uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#4A5D4A] uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($documents as $document)
                    @php
                        // Validity progress calculation
                        $daysLeft = $document->daysUntilExpiry();
                        $isExpired = $document->isExpired();
                        $expiryPercent = 100;
                        if ($document->issue_date && $document->expiry_date) {
                            $totalDays = max(1, $document->issue_date->diffInDays($document->expiry_date));
                            $remainingDays = max(0, now()->diffInDays($document->expiry_date, false));
                            $expiryPercent = min(100, max(0, intval(($remainingDays / $totalDays) * 100)));
                        } elseif ($daysLeft !== null) {
                            $expiryPercent = max(0, min(100, intval(($daysLeft / 365) * 100)));
                        }
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-[#E1E8E1] rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden shadow-sm">
                                    <img src="{{ asset('assets/images/' . ($typeImages[$document->type] ?? 'unnamed.webp')) }}" alt="{{ $types[$document->type] }}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                        {{ $document->title }}
                                        <span class="inline-flex items-center text-[10px] bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded font-mono">
                                            <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                            Encrypted
                                        </span>
                                    </div>
                                    @if($document->document_number)
                                        <div class="text-xs text-gray-500 font-mono mt-0.5">#{{ $document->document_number }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-[#E1E8E1] text-[#4A5D4A]">
                                {{ $types[$document->type] ?? $document->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="w-48">
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span>{{ $document->expiry_date?->format('d M Y') ?? 'No Expiry' }}</span>
                                    @if($document->expiry_date)
                                    <span class="font-medium text-gray-500">{{ $daysLeft !== null && $daysLeft >= 0 ? $daysLeft . ' days left' : 'Expired' }}</span>
                                    @endif
                                </div>
                                @if($document->expiry_date)
                                <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                    <div class="h-1.5 rounded-full transition-all duration-500 {{ $isExpired ? 'bg-red-500' : ($daysLeft <= 30 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                         style="width: {{ $isExpired ? 100 : $expiryPercent }}%"></div>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($isExpired)
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                Expired
                            </span>
                            @elseif($daysLeft !== null && $daysLeft <= 30)
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-amber-100 text-amber-700">
                                Expires Soon ({{ $daysLeft }}d)
                            </span>
                            @else
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                Active & Valid
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1">
                                <button wire:click="preview({{ $document->id }})"
                                        class="p-2 text-gray-500 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-all"
                                        title="Preview Document">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                <button wire:click="edit({{ $document->id }})"
                                        class="p-2 text-gray-500 hover:text-[#4A5D4A] hover:bg-[#E1E8E1] rounded-lg transition-all"
                                        data-bs-toggle="modal" data-bs-target="#documentModal"
                                        title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                @if($document->file_path)
                                <button wire:click="download({{ $document->id }})"
                                        class="p-2 text-gray-500 hover:text-[#4A5D4A] hover:bg-[#E1E8E1] rounded-lg transition-all"
                                        title="Download">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </button>
                                @endif
                                <button wire:click="confirmDelete({{ $document->id }})"
                                        class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                        title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($documents->isEmpty())
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-500 font-medium">No documents found in Digital Locker</p>
            <button wire:click="resetForm"
                    class="mt-4 text-[#4A5D4A] font-medium hover:underline text-sm"
                    data-bs-toggle="modal" data-bs-target="#documentModal">
                + Add your first document
            </button>
        </div>
        @endif
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $documents->links() }}
        </div>
    </div>
    @endif

    {{-- Activity History Tab --}}
    @if($activeTab === 'history')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-[#E1E8E1]">
            <h3 class="font-bold text-[#4A5D4A] text-lg">Digital Locker Activity History</h3>
            <span class="text-xs text-[#4A5D4A] font-medium">Recorded Events</span>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($activityLogs as $log)
            <div class="p-4 hover:bg-gray-50 transition-colors flex items-center gap-4">
                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 {{
                    $log->action === 'document_added' ? 'bg-emerald-100 text-emerald-700' :
                    ($log->action === 'document_updated' ? 'bg-blue-100 text-blue-700' :
                    ($log->action === 'document_deleted' ? 'bg-red-100 text-red-700' : 'bg-purple-100 text-purple-700'))
                }}">
                    @if($log->action === 'document_added')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    @elseif($log->action === 'document_updated')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    @elseif($log->action === 'document_deleted')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    @else
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800">{{ $log->description }}</p>
                    <p class="text-xs text-gray-500 mt-0.5 flex items-center gap-3">
                        <span>IP: {{ $log->ip_address ?? '127.0.0.1' }}</span>
                        <span>•</span>
                        <span>{{ $log->created_at->format('d M Y, h:i A') }} ({{ $log->created_at->diffForHumans() }})</span>
                    </p>
                </div>
                <span class="text-xs px-2.5 py-1 rounded-full font-medium uppercase tracking-wider bg-gray-100 text-gray-700">
                    {{ str_replace('_', ' ', $log->action) }}
                </span>
            </div>
            @empty
            <div class="text-center py-12 text-gray-500">
                No activity recorded yet in Digital Locker history.
            </div>
            @endforelse
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $activityLogs->links() }}
        </div>
    </div>
    @endif

    {{-- Instant Document Preview Modal --}}
    @if($previewDocument)
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-[1060] p-4">
        <div class="bg-white rounded-2xl w-full max-w-4xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh] transition-all">
            <div class="px-6 py-4 bg-[#4A5D4A] text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">{{ $previewDocument->title }}</h3>
                        <p class="text-xs text-emerald-100 flex items-center gap-2">
                            <span>{{ $types[$previewDocument->type] ?? $previewDocument->type }}</span>
                            <span>•</span>
                            <span class="font-mono">#{{ $previewDecryptedData['document_number'] ?? $previewDocument->document_number ?? 'N/A' }}</span>
                        </p>
                    </div>
                </div>
                <button wire:click="closePreview" class="text-white/80 hover:text-white p-2 rounded-lg hover:bg-white/10 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto flex-1 space-y-6">
                {{-- Document Preview Container --}}
                @if($previewDataUri)
                    <div class="bg-gray-100 rounded-xl p-4 flex items-center justify-center border border-gray-200 min-h-[300px]">
                        @if(str_contains($previewDocument->mime_type, 'image'))
                            <img src="{{ $previewDataUri }}" alt="{{ $previewDocument->title }}" class="max-h-[500px] w-auto object-contain rounded-lg shadow-sm">
                        @elseif(str_contains($previewDocument->mime_type, 'pdf'))
                            <iframe src="{{ $previewDataUri }}" class="w-full h-[500px] rounded-lg border-0"></iframe>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-gray-700 font-medium">Binary Document File</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $previewDocument->file_name }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-xl text-sm flex items-center gap-3">
                        <svg class="w-6 h-6 flex-shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>No file attachment attached to this document record, or file content is encrypted securely.</span>
                    </div>
                @endif

                {{-- Decrypted Details Table --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Document Number</span>
                        <span class="font-bold text-gray-800 font-mono">{{ $previewDecryptedData['document_number'] ?? 'N/A' }}</span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Issue Date</span>
                        <span class="font-bold text-gray-800">{{ $previewDocument->issue_date?->format('d M Y') ?? 'N/A' }}</span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Expiry Date</span>
                        <span class="font-bold text-gray-800">{{ $previewDocument->expiry_date?->format('d M Y') ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                <span class="text-xs text-gray-500 flex items-center gap-1">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Protected by AES-256 Encryption
                </span>
                <div class="flex gap-3">
                    <button wire:click="closePreview" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-100 text-sm font-medium transition-colors">
                        Close
                    </button>
                    @if($previewDocument->file_path)
                    <button wire:click="download({{ $previewDocument->id }})" class="px-4 py-2 bg-[#4A5D4A] text-white rounded-xl hover:bg-[#3A4D3A] text-sm font-medium transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download Document
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Add/Edit Modal --}}
    <div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-2xl border-0 shadow-2xl">
                <form wire:submit="save">
                    <div class="modal-header border-0 px-6 py-4 bg-[#E1E8E1]">
                        <h5 class="modal-title text-xl font-bold text-[#4A5D4A]" id="documentModalLabel">
                            {{ $editId ? 'Edit Digital Locker Document' : 'Add Document to Digital Locker' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="resetForm"></button>
                    </div>
                    <div class="modal-body px-6 pb-6 pt-4">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Document Title</label>
                                <input wire:model="title" type="text" placeholder="e.g. Citizenship Card, Passport, Driving License"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#4A5D4A] focus:border-[#4A5D4A] outline-none transition-all text-sm @error('title') border-red-500 @enderror">
                                @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Document Category / Type</label>
                                <select wire:model="type"
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#4A5D4A] focus:border-[#4A5D4A] outline-none transition-all text-sm @error('type') border-red-500 @enderror">
                                    <option value="">Select type</option>
                                    @foreach($types as $typeKey => $typeLabel)
                                    <option value="{{ $typeKey }}">{{ $typeLabel }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Document Number (Encrypted)</label>
                                <input wire:model="document_number" type="text" placeholder="e.g. 06-01-78-12345"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#4A5D4A] focus:border-[#4A5D4A] outline-none transition-all text-sm font-mono">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Issue Date</label>
                                    <input wire:model="issue_date" type="date"
                                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#4A5D4A] focus:border-[#4A5D4A] outline-none transition-all text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                                    <input wire:model="expiry_date" type="date"
                                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#4A5D4A] focus:border-[#4A5D4A] outline-none transition-all text-sm">
                                </div>
                            </div>

                            {{-- Dynamic File Upload with Progress Bar & Instant Preview --}}
                            <div x-data="{ uploading: false, progress: 0, previewUrl: null, fileName: '', fileSize: '' }"
                                 x-on:livewire-upload-start="uploading = true; progress = 0"
                                 x-on:livewire-upload-finish="uploading = false"
                                 x-on:livewire-upload-error="uploading = false"
                                 x-on:livewire-upload-progress="progress = $event.detail.progress">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Document File (PDF, JPG, PNG, HEIC - Max 10MB)</label>
                                <input wire:model="file" type="file"
                                       @change="
                                           const f = $event.target.files[0];
                                           if (f) {
                                               fileName = f.name;
                                               fileSize = (f.size / 1024).toFixed(1) + ' KB';
                                               if (f.type.startsWith('image/')) {
                                                   const reader = new FileReader();
                                                   reader.onload = (e) => { previewUrl = e.target.result; };
                                                   reader.readAsDataURL(f);
                                               } else {
                                                   previewUrl = null;
                                               }
                                           }
                                       "
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#4A5D4A] focus:border-[#4A5D4A] outline-none transition-all text-sm bg-gray-50 @error('file') border-red-500 @enderror">
                                @error('file')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror

                                {{-- Real-Time Upload Progress Bar --}}
                                <div x-show="uploading" x-cloak class="mt-3 bg-gray-50 p-3 rounded-xl border border-gray-200">
                                    <div class="flex justify-between text-xs font-semibold text-[#4A5D4A] mb-1">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Encrypting & Uploading...
                                        </span>
                                        <span x-text="progress + '%'"></span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-[#4A5D4A] h-2 rounded-full transition-all duration-300" :style="'width: ' + progress + '%'"></div>
                                    </div>
                                </div>

                                {{-- Instant File Preview Box Before Submit --}}
                                <template x-if="fileName">
                                    <div class="mt-3 p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3">
                                        <template x-if="previewUrl">
                                            <img :src="previewUrl" class="w-12 h-12 object-cover rounded-lg border border-emerald-300 shadow-sm">
                                        </template>
                                        <template x-if="!previewUrl">
                                            <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-800 font-bold text-xs uppercase">
                                                DOCUMENT
                                            </div>
                                        </template>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-gray-800 truncate" x-text="fileName"></p>
                                            <p class="text-[11px] text-gray-500" x-text="fileSize"></p>
                                        </div>
                                        <span class="text-[11px] bg-emerald-600 text-white px-2.5 py-0.5 rounded-full font-medium">Selected for Locker</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-6 py-4 gap-3 bg-gray-50">
                        <button type="button" class="px-5 py-2.5 text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-100 transition-all text-sm font-medium"
                                data-bs-dismiss="modal" wire:click="resetForm">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-[#4A5D4A] text-white rounded-xl hover:bg-[#3A4D3A] transition-all font-medium text-sm shadow-sm">
                            {{ $editId ? 'Update Document' : 'Save to Digital Locker' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    @if($confirmDeleteId)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-[1050]">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4 shadow-2xl">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Delete Locker Document</h3>
                <p class="text-gray-500 mb-6 text-sm">Are you sure you want to delete this document from your Digital Locker? This action will move it to quarantine.</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="$set('confirmDeleteId', null)"
                            class="px-5 py-2.5 text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all text-sm">
                        Cancel
                    </button>
                    <button wire:click="delete"
                            class="px-5 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all font-medium text-sm">
                        Delete Document
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
