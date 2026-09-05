@extends('admin.layouts.app')

@section('title', 'Document Templates')
@section('subtitle', 'Manage templates for Citizenship, National ID, Driving License, Passport, etc.')

@section('content')
<div class="flex justify-between items-start mb-6 flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Document Templates</h1>
        <p class="text-sm text-gray-600">Create downloadable form templates and pre-filled data blueprints for citizens.</p>
    </div>
    <a href="{{ route('admin.document-templates.create') }}"
       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm flex items-center gap-2 whitespace-nowrap">
        <span>+ Add New Template</span>
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" action="{{ route('admin.document-templates.index') }}" class="flex gap-3 flex-wrap items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search template title or description..."
                   class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Category</label>
            <select name="category" class="border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border bg-white">
                <option value="">All Categories</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
            <select name="status" class="border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border bg-white">
                <option value="">All</option>
                <option value="active"   {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">Filter</button>
        @if(request()->hasAny(['category','status','search']))
            <a href="{{ route('admin.document-templates.index') }}"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">Clear</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">ID</th>
                <th class="py-3 px-4">Title / Slug</th>
                <th class="py-3 px-4">Category</th>
                <th class="py-3 px-4">File</th>
                <th class="py-3 px-4">Fields</th>
                <th class="py-3 px-4">Featured</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4">Downloads</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($templates as $tpl)
            <tr class="hover:bg-gray-50 transition align-top">
                <td class="py-3 px-4 text-gray-500 font-mono text-xs">#{{ $tpl->id }}</td>
                <td class="py-3 px-4">
                    <div class="font-semibold text-gray-900">{{ $tpl->title }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">
                        <span class="inline-block max-w-[240px] truncate align-bottom">slug: <code class="text-[11px] bg-gray-50 px-1 py-0.5 rounded">{{ $tpl->slug }}</code></span>
                        @if($tpl->description)
                            <div class="text-gray-500 mt-1 max-w-sm line-clamp-2">{{ $tpl->description }}</div>
                        @endif
                    </div>
                </td>
                <td class="py-3 px-4">
                    @if($tpl->category)
                        <span class="px-2 py-1 text-xs font-medium rounded bg-indigo-50 text-indigo-700">
                            {{ $categories[$tpl->category] ?? ucfirst($tpl->category) }}
                        </span>
                    @else
                        <span class="text-gray-400 text-xs">—</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-xs text-gray-600">
                    @if($tpl->template_file_path)
                        <a href="{{ Storage::disk('public')->url($tpl->template_file_path) }}" target="_blank" class="text-blue-600 hover:underline block max-w-[160px] truncate" title="{{ $tpl->template_file_name }}">
                            {{ $tpl->template_file_name }}
                        </a>
                        <div class="text-gray-400 mt-1">{{ number_format($tpl->template_file_size / 1024, 1) }} KB</div>
                    @else
                        <span class="text-amber-600 text-xs">No file uploaded</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-xs">
                    @if($tpl->placeholder_fields && count($tpl->placeholder_fields))
                        <span class="font-semibold text-gray-800">{{ count($tpl->placeholder_fields) }}</span>
                        <div class="text-gray-500 mt-1 space-x-1">
                            @foreach($tpl->placeholder_fields as $f)
                                <span class="inline-block bg-gray-50 text-gray-600 px-1.5 py-0.5 rounded text-[10px]">{{ $f['name'] }}</span>
                                @if(!$loop->last && $loop->iteration < 4) • @endif
                                @if($loop->iteration === 4 && count($tpl->placeholder_fields) > 4)
                                    <span class="text-gray-400">+{{ count($tpl->placeholder_fields) - 4 }}</span>
                                    @break
                                @endif
                            @endforeach
                        </div>
                    @else
                        <span class="text-gray-400">0</span>
                    @endif
                </td>
                <td class="py-3 px-4">
                    @if($tpl->is_featured)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">⭐ Featured</span>
                    @else
                        <span class="text-gray-400 text-xs">No</span>
                    @endif
                </td>
                <td class="py-3 px-4">
                    @if($tpl->is_active)
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Inactive</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-sm text-gray-700 font-mono">{{ $tpl->download_count }}</td>
                <td class="py-3 px-4 text-right space-x-2 whitespace-nowrap">
                    <a href="{{ route('admin.document-templates.show', $tpl) }}" class="text-blue-600 hover:text-blue-800 font-medium text-xs">View</a>
                    <a href="{{ route('admin.document-templates.edit', $tpl) }}" class="text-blue-600 hover:text-blue-800 font-medium text-xs">Edit</a>
                    <form action="{{ route('admin.document-templates.destroy', $tpl) }}" method="POST" class="inline" onsubmit="return confirm('Delete this template permanently?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-xs">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="py-10 text-center text-gray-500 text-sm">
                    No document templates yet. <a href="{{ route('admin.document-templates.create') }}" class="text-blue-600 hover:underline font-medium">Create your first template</a> (e.g. Driving License form, Citizenship application, National ID template, Passport form).
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">
        {{ $templates->links() }}
    </div>
</div>
@endsection
