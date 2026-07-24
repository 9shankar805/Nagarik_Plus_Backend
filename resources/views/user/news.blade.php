@extends('layouts.user')

@section('title', 'News & Updates')
@section('subtitle', 'Stay informed with the latest news')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Category Filter --}}
    <div class="flex flex-wrap gap-3 mb-8">
        <a href="{{ route('user.news') }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ !request('category') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            All
        </a>
        @foreach($categories as $category)
        <a href="{{ route('user.news', ['category' => $category]) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('category') === $category ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            {{ $category }}
        </a>
        @endforeach
    </div>

    {{-- News List --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($news as $item)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            @if($item->image_url)
            <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-48 object-cover">
            @endif
            <div class="p-6">
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">{{ $item->category }}</span>
                    @if($item->is_featured)
                    <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded">Featured</span>
                    @endif
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $item->title }}</h3>
                <p class="text-sm text-gray-600 line-clamp-3 mb-4">{{ $item->content }}</p>
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>{{ $item->published_at?->format('d M Y') }}</span>
                    <span>{{ $item->source }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $news->links() }}
    </div>
</div>
@endsection
