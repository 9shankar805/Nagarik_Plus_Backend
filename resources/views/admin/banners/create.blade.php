@extends('admin.layouts.app')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.banners.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Banners</a>
    <h1 class="text-2xl font-bold text-gray-900 mt-2">Add New Banner Slide</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title (English) <span class="text-red-500">*</span></label>
            <input type="text" name="title" required placeholder="e.g. File Police Reports Easily via Nagarik+"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title (Nepali)</label>
            <input type="text" name="title_np" placeholder="नागरिक+ मार्फत सजिलै प्रहरी रिपोर्ट फाइल गर्नुहोस्"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3" placeholder="Brief description of the banner message"
                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Banner Image</label>
            <input type="file" name="image" accept="image/jpeg,image/jpg,image/png,image/webp"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            <p class="text-xs text-gray-500 mt-1">Upload image (JPG, PNG, WEBP - Max 5MB)</p>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Or Image URL</label>
            <input type="url" name="image_url" placeholder="https://example.com/banner.jpg"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Link Type <span class="text-red-500">*</span></label>
                <select name="link_type" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="none">None</option>
                    <option value="news">News Article</option>
                    <option value="service">Citizen Service</option>
                    <option value="external">External URL</option>
                    <option value="document">Document</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="0" min="0"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Link Value</label>
            <input type="text" name="link_value" placeholder="ID, slug, or full URL depending on link type"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Starts At</label>
                <input type="datetime-local" name="starts_at"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Ends At</label>
                <input type="datetime-local" name="ends_at"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-blue-600 rounded">
                <span class="text-sm font-medium text-gray-700">Publish & Make Active</span>
            </label>
        </div>

        <div class="pt-4 flex gap-3">
            <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                Save Banner Slide
            </button>
            <a href="{{ route('admin.banners.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
