@extends('admin.layouts.app')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.social-services.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Social Services</a>
    <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit Social Service Card #{{ $socialService->id }}</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form action="{{ route('admin.social-services.update', $socialService) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title (English)</label>
            <input type="text" name="title_en" value="{{ $socialService->title_en }}" required
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title (Nepali)</label>
            <input type="text" name="title_np" value="{{ $socialService->title_np }}"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Subtitle (English)</label>
            <input type="text" name="subtitle_en" value="{{ $socialService->subtitle_en }}"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Subtitle (Nepali)</label>
            <input type="text" name="subtitle_np" value="{{ $socialService->subtitle_np }}"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Icon Key</label>
                <input type="text" name="icon" value="{{ $socialService->icon }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Hex Color</label>
                <input type="text" name="color" value="{{ $socialService->color }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" @if($socialService->is_active) checked @endif class="w-4 h-4 text-blue-600 rounded">
                <span class="text-sm font-medium text-gray-700">Active</span>
            </label>
        </div>

        <div class="pt-4 flex gap-3">
            <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                Update Card
            </button>
            <a href="{{ route('admin.social-services.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
