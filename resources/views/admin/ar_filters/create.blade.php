@extends('admin.layouts.app')

@section('title', 'Add AR Filter')
@section('header', 'Add New AR Filter')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form action="{{ route('admin.ar-filters.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" name="title" required value="{{ old('title') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">AR Engine</label>
            <select name="engine" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="deepar" {{ old('engine') == 'deepar' ? 'selected' : '' }}>DeepAR</option>
                <option value="banuba" {{ old('engine') == 'banuba' ? 'selected' : '' }}>Banuba</option>
                <option value="custom" {{ old('engine') == 'custom' ? 'selected' : '' }}>Custom / Other</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Upload Filter File (e.g. .deepar)</label>
            <input type="file" name="filter_file" class="mt-1 block w-full">
            <p class="text-sm text-gray-500 mt-1">Or provide a URL below.</p>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Filter File URL (Optional)</label>
            <input type="url" name="filter_file_url_text" value="{{ old('filter_file_url_text') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Upload Thumbnail</label>
            <input type="file" name="thumbnail" accept="image/*" class="mt-1 block w-full">
        </div>

        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-600">Active</span>
            </label>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Save Filter</button>
            <a href="{{ route('admin.ar-filters.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancel</a>
        </div>
    </form>
</div>
@endsection
