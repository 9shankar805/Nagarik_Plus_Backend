@extends('admin.layouts.app')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.banners.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Banners</a>
    <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit Banner Slide #{{ $banner->id }}</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title (English) <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $banner->title) }}" required
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title (Nepali)</label>
            <input type="text" name="title_np" value="{{ old('title_np', $banner->title_np) }}"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3"
                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('description', $banner->description) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Banner Image</label>
            <input type="file" name="image" accept="image/jpeg,image/jpg,image/png,image/webp"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            <p class="text-xs text-gray-500 mt-1">Upload image (JPG, PNG, WEBP - Max 5MB)</p>
            @if($banner->image_url)
                <div class="mt-2">
                    <img src="{{ asset($banner->image_url) }}" alt="Current banner" class="h-20 rounded border">
                    <p class="text-xs text-gray-500 mt-1">Current: {{ basename($banner->image_url) }}</p>
                </div>
            @endif
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Or Image URL</label>
            <input type="url" name="image_url" value="{{ old('image_url', $banner->image_url) }}"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Link Type <span class="text-red-500">*</span></label>
                <select name="link_type" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="none" {{ old('link_type', $banner->link_type) == 'none' ? 'selected' : '' }}>None</option>
                    <option value="news" {{ old('link_type', $banner->link_type) == 'news' ? 'selected' : '' }}>News Article</option>
                    <option value="service" {{ old('link_type', $banner->link_type) == 'service' ? 'selected' : '' }}>Citizen Service</option>
                    <option value="external" {{ old('link_type', $banner->link_type) == 'external' ? 'selected' : '' }}>External URL</option>
                    <option value="document" {{ old('link_type', $banner->link_type) == 'document' ? 'selected' : '' }}>Document</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order) }}" min="0"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Link Value</label>
            <input type="text" name="link_value" value="{{ old('link_value', $banner->link_value) }}"
                   placeholder="ID, slug, or full URL depending on link type"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Starts At</label>
                <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $banner->starts_at ? $banner->starts_at->format('Y-m-d\TH:i') : '') }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Ends At</label>
                <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $banner->ends_at ? $banner->ends_at->format('Y-m-d\TH:i') : '') }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                <span class="text-sm font-medium text-gray-700">Publish & Make Active</span>
            </label>
        </div>

        <div class="pt-4 flex gap-3">
            <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                Update Banner Slide
            </button>
            <a href="{{ route('admin.banners.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
