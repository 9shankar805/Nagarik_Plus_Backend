@extends('admin.layouts.app')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.shorts.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Shorts</a>
    <h1 class="text-2xl font-bold text-gray-900 mt-2">Upload Educational Video Short</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form action="{{ route('admin.shorts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title (English)</label>
            <input type="text" name="title_en" required placeholder="e.g. How to check Driving License Trial status"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title (Nepali)</label>
            <input type="text" name="title_np" placeholder="उदा. सवारी चालक अनुमति पत्रको ट्रायल अवस्था कसरी हेर्ने"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
            <select name="category" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="traffic_rules">Traffic Rules & Signs</option>
                <option value="license_prep">Driving License Preparation</option>
                <option value="citizen_guides">Citizen Services Guide</option>
                <option value="passport_guides">Passport & Visa Guide</option>
            </select>
        </div>

        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-3">
            <h3 class="text-sm font-bold text-gray-800">Video Upload Source</h3>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Upload MP4 Video File</label>
                <input type="file" name="video_file" accept="video/mp4,video/webm,video/quicktime"
                       class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <div class="text-center text-xs text-gray-400 font-semibold uppercase">— OR —</div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Video Stream URL</label>
                <input type="url" name="video_url_text" placeholder="https://..."
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-xs">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Thumbnail Cover Image (Optional)</label>
            <input type="file" name="thumbnail" accept="image/*"
                   class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Description (English)</label>
            <textarea name="description_en" rows="3" placeholder="Brief summary of the tutorial short..."
                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_published" value="1" checked class="w-4 h-4 text-blue-600 rounded">
                <span class="text-sm font-medium text-gray-700">Publish immediately to mobile app</span>
            </label>
        </div>

        <div class="pt-4 flex gap-3">
            <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                Upload & Publish Video Short
            </button>
            <a href="{{ route('admin.shorts.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
