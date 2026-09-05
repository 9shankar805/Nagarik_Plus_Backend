@extends('admin.layouts.app')

@section('title', 'Edit AR Filter')
@section('header', 'Edit AR Filter')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form action="{{ route('admin.ar-filters.update', $arFilter) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" name="title" required value="{{ old('title', $arFilter->title) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">AR Engine</label>
            <select name="engine" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="deepar" {{ old('engine', $arFilter->engine) == 'deepar' ? 'selected' : '' }}>DeepAR</option>
                <option value="banuba" {{ old('engine', $arFilter->engine) == 'banuba' ? 'selected' : '' }}>Banuba</option>
                <option value="custom" {{ old('engine', $arFilter->engine) == 'custom' ? 'selected' : '' }}>Custom / Other</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Upload New Filter File (Optional)</label>
            <input type="file" name="filter_file" class="mt-1 block w-full">
            @if($arFilter->filter_file_url)
                <p class="text-sm text-gray-500 mt-1">Current file: <a href="{{ $arFilter->filter_file_url }}" target="_blank" class="text-indigo-600 hover:underline">Download/View</a></p>
            @endif
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Filter File URL (Optional)</label>
            <input type="url" name="filter_file_url_text" value="{{ old('filter_file_url_text') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Upload New Thumbnail (Optional)</label>
            <input type="file" name="thumbnail" accept="image/*" class="mt-1 block w-full">
            @if($arFilter->thumbnail_url)
                <img src="{{ $arFilter->thumbnail_url }}" alt="Thumbnail" class="mt-2 h-20 rounded">
            @endif
        </div>

        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $arFilter->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-600">Active</span>
            </label>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Update Filter</button>
            <a href="{{ route('admin.ar-filters.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancel</a>
        </div>
    </form>
</div>
@endsection
