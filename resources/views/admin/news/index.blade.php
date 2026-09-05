@extends('admin.layouts.app')

@section('title', 'News & Shorts Moderation')
@section('subtitle', 'Approve or reject user-submitted news and shorts, and manage published content')

@section('content')

<div class="flex justify-between items-start mb-6 flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">News & Shorts</h1>
        <p class="text-sm text-gray-600 mt-1">
            Review user-submitted content below. New submissions appear as <span class="font-semibold text-amber-600">Pending</span>.
        </p>
    </div>
    <a href="{{ route('admin.news.create') }}"
       class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
        + New Article
    </a>
</div>

{{-- Status tabs with counts --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-1 mb-6 inline-flex flex-wrap">
    @php
        $tabs = [
            ''          => ['All',      $counts['pending'] + $counts['approved'] + $counts['rejected'], ''],
            'pending'   => ['Pending',  $counts['pending'],                               'pending'],
            'approved'  => ['Approved', $counts['approved'],                              'approved'],
            'rejected'  => ['Rejected', $counts['rejected'],                              'rejected'],
        ];
        $currentStatus = request('status', '');
    @endphp
    @foreach($tabs as $status => [$label, $count, $val])
        <a href="{{ route('admin.news.index', array_filter(request()->query() + ['status' => $val])) }}"
           class="px-4 py-2 text-sm font-medium rounded-lg transition-colors inline-flex items-center gap-2
              {{ $currentStatus === $val ? 'bg-blue-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
            {{ $label }}
            <span class="inline-flex items-center justify-center min-w-[22px] h-5 px-1.5 text-[11px] font-bold rounded-full
                {{ $currentStatus === $val ? 'bg-white/20 text-white' : ($val === 'pending' ? 'bg-amber-100 text-amber-700' : ($val === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700')) }}">
                {{ $count }}
            </span>
        </a>
    @endforeach
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" action="{{ route('admin.news.index') }}" class="flex gap-3 flex-wrap items-end">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <div class="flex-1 min-w-[220px]">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Search Title / Content</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search articles..."
                   class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Type</label>
            <select name="type" class="border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border bg-white">
                <option value="">All</option>
                <option value="news"  {{ request('type') === 'news'  ? 'selected' : '' }}>News Articles</option>
                <option value="short" {{ request('type') === 'short' ? 'selected' : '' }}>Shorts</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Category</label>
            <select name="category" class="border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border bg-white">
                <option value="">All</option>
                @foreach(['notice'=>'Notice','service'=>'Service','exam'=>'Exam','deadline'=>'Deadline','update'=>'Update'] as $k=>$l)
                    <option value="{{ $k }}" {{ request('category') === $k ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">Filter</button>
        @if(request()->hasAny(['category','type','search','status']))
            <a href="{{ route('admin.news.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">Clear</a>
        @endif
    </form>
</div>

{{-- Data table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type / Category</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted By</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Featured</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($news as $article)
                    <tr class="hover:bg-gray-50 transition-colors align-top
                        {{ $article->status === \App\Models\News::STATUS_PENDING ? 'bg-amber-50/30 hover:bg-amber-50' : '' }}
                        {{ $article->status === \App\Models\News::STATUS_REJECTED ? 'opacity-75' : '' }}">
                        <td class="px-4 py-3">
                            <div class="flex gap-3 items-start">
                                @if($article->image_url)
                                    <img src="{{ $article->image_url }}" alt="" class="w-14 h-14 rounded-lg object-cover flex-shrink-0 bg-gray-100 border border-gray-100" onerror="this.style.display='none'">
                                @else
                                    <div class="w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 flex-shrink-0 border border-gray-100">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 max-w-sm truncate">{{ $article->title }}</p>
                                    @if($article->title_np)
                                        <p class="text-xs text-gray-500 mt-0.5 max-w-sm truncate">{{ $article->title_np }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $article->source }} · {{ $article->published_at?->format('d M Y') ?? $article->created_at->format('d M Y') }}
                                    </p>
                                    @if($article->status === \App\Models\News::STATUS_REJECTED && $article->rejection_reason)
                                        <div class="mt-2 inline-block max-w-sm">
                                            <span class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-medium rounded bg-red-50 text-red-700 border border-red-100">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                Rejected: {{ Str::limit($article->rejection_reason, 120) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1.5">
                                <span class="inline-flex px-2 py-0.5 text-[11px] font-semibold rounded-full
                                    {{ $article->is_short ? 'bg-purple-100 text-purple-700' : 'bg-indigo-50 text-indigo-700' }}">
                                    {{ $article->is_short ? '📱 Short' : '📰 Article' }}
                                </span>
                                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full
                                    {{ $article->category === 'notice' ? 'bg-yellow-100 text-yellow-700' :
                                       ($article->category === 'exam' ? 'bg-purple-100 text-purple-700' :
                                       ($article->category === 'deadline' ? 'bg-red-100 text-red-700' :
                                       ($article->category === 'service' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'))) }}">
                                    {{ ucfirst($article->category) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($article->author)
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold flex-shrink-0 overflow-hidden">
                                        @if($article->author->profile_photo)
                                            <img src="{{ $article->author->profile_photo_url ?? Storage::disk('public')->url($article->author->profile_photo) }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            {{ mb_substr($article->author->name ?? $article->author->email, 0, 1) }}
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-xs font-medium text-gray-800 truncate">{{ $article->author->name ?? '—' }}</div>
                                        <div class="text-[10px] text-gray-500 truncate">{{ $article->author->email }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">Admin posted</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($article->status === \App\Models\News::STATUS_PENDING)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 ring-1 ring-amber-200">
                                    ⏳ Pending Review
                                </span>
                            @elseif($article->status === \App\Models\News::STATUS_APPROVED)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    ✓ Approved
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    ✗ Rejected
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($article->is_featured)
                                <span class="inline-block w-2.5 h-2.5 bg-yellow-400 rounded-full shadow-[0_0_0_2px_rgba(251,191,36,0.25)]"></span>
                            @else
                                <span class="inline-block w-2.5 h-2.5 bg-gray-300 rounded-full"></span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                @if($article->status === \App\Models\News::STATUS_PENDING)
                                    <form method="POST" action="{{ route('admin.news.approve', $article) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="px-3 py-1.5 text-xs font-semibold bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-sm">
                                            ✓ Approve
                                        </button>
                                    </form>
                                    <button type="button"
                                            data-reject-url="{{ route('admin.news.reject', $article) }}"
                                            class="reject-trigger px-3 py-1.5 text-xs font-semibold bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                        ✗ Reject
                                    </button>
                                @elseif($article->status === \App\Models\News::STATUS_REJECTED)
                                    <form method="POST" action="{{ route('admin.news.send-back', $article) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="px-3 py-1.5 text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 rounded-lg hover:bg-amber-100 transition-colors">
                                            ↺ Back to Pending
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('admin.news.edit', $article) }}"
                                   class="px-3 py-1.5 text-xs font-medium text-blue-600 hover:text-blue-800 border border-blue-200 hover:border-blue-400 rounded-lg transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.news.destroy', $article) }}"
                                      onsubmit="return confirm('Delete this article?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1.5 text-xs font-medium text-red-600 hover:text-red-800 border border-red-200 hover:border-red-400 rounded-lg transition-colors">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            {{ request('status') === 'pending' ? 'No pending submissions 🎉' : 'No articles yet.' }}
                            <a href="{{ route('admin.news.create') }}" class="text-blue-600 hover:underline">Create one.</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($news->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $news->links() }}
        </div>
    @endif
</div>

{{-- Reject modal --}}
<div id="reject-modal" class="fixed inset-0 z-50 items-center justify-center p-4 bg-black/40 backdrop-blur-[2px] hidden">
    <form id="reject-form" method="POST" class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
        @csrf
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3 bg-red-50/50">
            <div class="w-9 h-9 rounded-full bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-gray-900">Reject Submission</h2>
                <p class="text-xs text-gray-500">The submitter will see the reason you enter below.</p>
            </div>
        </div>
        <div class="p-6">
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Reason for rejection <span class="text-red-500">*</span></label>
            <textarea name="reason" rows="4" required minlength="5" maxlength="1000"
                      placeholder="Be specific so they can resubmit: e.g. News is from unverified source, image is low quality, fact-check needed for X claim, duplicate post, etc."
                      class="w-full border-gray-300 rounded-lg text-sm focus:ring-red-500 focus:border-red-500 px-3 py-2 border"></textarea>
            <p class="text-xs text-gray-400 mt-1 text-right"><span id="reject-count">0</span>/1000</p>
        </div>
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
            <button type="button" id="reject-cancel"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            <button type="submit"
                    class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition shadow-sm">Confirm Reject</button>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
(function () {
    const modal  = document.getElementById('reject-modal');
    const form   = document.getElementById('reject-form');
    const cancel = document.getElementById('reject-cancel');
    const count  = document.getElementById('reject-count');
    const textArea = form?.querySelector('textarea[name="reason"]');
    function open(url) {
        if (!modal || !form) return;
        form.action = url;
        if (textArea) { textArea.value = ''; count.textContent = '0'; }
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        textArea?.focus();
    }
    function close() {
        modal?.classList.add('hidden');
        modal?.classList.remove('flex');
    }
    document.querySelectorAll('.reject-trigger').forEach(btn => {
        btn.addEventListener('click', () => open(btn.dataset.rejectUrl));
    });
    cancel?.addEventListener('click', close);
    modal?.addEventListener('click', (e) => { if (e.target === modal) close(); });
    textArea?.addEventListener('input', () => count.textContent = String(textArea.value.length));
})();
</script>
@endsection
