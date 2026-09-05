@extends('admin.layouts.app')

@section('title', $template->title)

@section('content')
<div class="mb-6 flex justify-between items-start flex-wrap gap-3">
    <div>
        <a href="{{ route('admin.document-templates.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Templates</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $template->title }}</h1>
        <p class="text-sm text-gray-600 mt-1">
            Category: <span class="font-semibold">{{ \App\Models\DocumentTemplate::CATEGORIES[$template->category] ?? $template->category ?? '—' }}</span>
            · Status:
            @if($template->is_active)
                <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full bg-green-100 text-green-800">Active</span>
            @else
                <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full bg-gray-100 text-gray-600">Inactive</span>
            @endif
            @if($template->is_featured)
                · <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full bg-amber-100 text-amber-800">⭐ Featured</span>
            @endif
            · <span class="text-gray-500">Slug:</span> <code class="text-xs bg-gray-50 px-1.5 py-0.5 rounded">{{ $template->slug }}</code>
            · Sort order: {{ $template->sort_order }}
            · Downloads: {{ $template->download_count }}
        </p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.document-templates.edit', $template) }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">Edit</a>
        <form action="{{ route('admin.document-templates.destroy', $template) }}" method="POST" class="inline" onsubmit="return confirm('Delete this template permanently?')">
            @csrf
            @method('DELETE')
            <button class="px-4 py-2 bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 transition font-medium text-sm">Delete</button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-semibold text-gray-900 mb-3">Description</h2>
            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $template->description ?: 'No description provided.' }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-semibold text-gray-900 mb-3">Placeholder Fields <span class="text-xs text-gray-500 font-normal">({{ count($template->placeholder_fields ?? []) }} fields)</span></h2>
            @if($template->placeholder_fields && count($template->placeholder_fields))
                <div class="divide-y divide-gray-100 border border-gray-100 rounded-lg overflow-hidden">
                    @foreach($template->placeholder_fields as $i => $f)
                        <div class="flex items-center gap-3 px-4 py-2.5 bg-gray-50/0 hover:bg-gray-50 transition">
                            <div class="text-xs font-mono text-gray-400 w-6">{{ $i + 1 }}.</div>
                            <div class="flex-1">
                                <div class="font-medium text-sm text-gray-900">
                                    {{ $f['label'] }}
                                    @if(!empty($f['required']))
                                        <span class="text-red-500">*</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 font-mono mt-0.5">{{ $f['name'] }}</div>
                            </div>
                            <span class="px-2 py-0.5 text-[11px] rounded bg-indigo-50 text-indigo-700 font-medium">{{ $f['type'] }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">No placeholder fields defined.</p>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-semibold text-gray-900 mb-3">Filling Guidelines</h2>
            <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $template->guidelines ?: 'No guidelines provided yet.' }}</p>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-semibold text-gray-900 mb-3">Template File</h2>
            @if($template->template_file_path)
                <div class="flex items-start gap-3 p-3 bg-blue-50/50 border border-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <div class="flex-1 min-w-0">
                        <a href="{{ Storage::disk('public')->url($template->template_file_path) }}" target="_blank"
                           class="text-blue-700 hover:underline font-medium break-all text-sm">{{ $template->template_file_name }}</a>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ number_format(($template->template_file_size ?? 0) / 1024, 1) }} KB
                        </p>
                    </div>
                </div>
            @else
                <p class="text-sm text-amber-700 bg-amber-50 border border-amber-100 rounded-lg p-3">
                    ⚠️ No file uploaded yet. Edit the template and attach a PDF/DOCX/etc. so citizens can download it.
                </p>
            @endif
            @if($template->preview_image)
                <div class="mt-4">
                    <p class="text-xs text-gray-500 mb-2 font-semibold uppercase">Preview Image</p>
                    <img src="{{ $template->preview_image }}" alt="preview" class="rounded-lg border border-gray-200 max-h-48 object-cover w-full">
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-xs text-gray-500 space-y-1.5">
            <h2 class="font-semibold text-gray-900 text-sm mb-2">Metadata</h2>
            <p><span class="text-gray-400">Created:</span> {{ $template->created_at?->format('M j, Y g:i A') ?? '—' }}</p>
            <p><span class="text-gray-400">Updated:</span> {{ $template->updated_at?->format('M j, Y g:i A') ?? '—' }}</p>
            <p><span class="text-gray-400">API endpoint list:</span> <code class="bg-gray-50 px-1 rounded">GET /api/v1/document-templates</code></p>
            <p><span class="text-gray-400">API endpoint detail:</span> <code class="bg-gray-50 px-1 rounded">GET /api/v1/document-templates/{slug}</code></p>
            <p><span class="text-gray-400">API download:</span> <code class="bg-gray-50 px-1 rounded">GET /api/v1/document-templates/{slug}/download</code></p>
        </div>
    </div>
</div>
@endsection
