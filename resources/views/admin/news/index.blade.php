@extends('admin.layouts.app')

@section('title', 'News')
@section('subtitle', 'Manage news and announcements')

@section('content')

<div class="flex justify-end mb-6">
    <a href="{{ route('admin.news.create') }}"
       class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
        + New Article
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Published</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Featured</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Verified</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($news as $article)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3">
                            <p class="font-medium text-gray-800 max-w-xs truncate">{{ $article->title }}</p>
                            @if($article->published_at)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $article->published_at->format('d M Y') }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full
                                {{ $article->category === 'notice' ? 'bg-yellow-100 text-yellow-700' :
                                   ($article->category === 'exam' ? 'bg-purple-100 text-purple-700' :
                                   ($article->category === 'deadline' ? 'bg-red-100 text-red-700' :
                                   ($article->category === 'service' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'))) }}">
                                {{ ucfirst($article->category) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $article->source }}</td>
                        <td class="px-6 py-3 text-center">
                            @if($article->is_published)
                                <span class="inline-block w-2 h-2 bg-green-400 rounded-full"></span>
                            @else
                                <span class="inline-block w-2 h-2 bg-gray-300 rounded-full"></span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-center">
                            @if($article->is_featured)
                                <span class="inline-block w-2 h-2 bg-yellow-400 rounded-full"></span>
                            @else
                                <span class="inline-block w-2 h-2 bg-gray-300 rounded-full"></span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-center">
                            @if($article->is_verified)
                                <span class="inline-block w-2 h-2 bg-blue-400 rounded-full"></span>
                            @else
                                <span class="inline-block w-2 h-2 bg-gray-300 rounded-full"></span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.news.edit', $article) }}"
                                   class="px-3 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 border border-blue-200 hover:border-blue-400 rounded-lg transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.news.destroy', $article) }}"
                                      onsubmit="return confirm('Delete this article?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1 text-xs font-medium text-red-600 hover:text-red-800 border border-red-200 hover:border-red-400 rounded-lg transition-colors">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">No articles yet. <a href="{{ route('admin.news.create') }}" class="text-blue-600 hover:underline">Create one.</a></td>
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

@endsection
