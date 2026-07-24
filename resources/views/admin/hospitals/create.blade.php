@extends('admin.layouts.app')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.hospitals.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Hospitals</a>
    <h1 class="text-2xl font-bold text-gray-900 mt-2">Add Emergency Hospital</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form action="{{ route('admin.hospitals.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Hospital Name (English)</label>
            <input type="text" name="name" required placeholder="e.g. Teaching Hospital"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Hospital Name (Nepali)</label>
            <input type="text" name="name_np" placeholder="उदा. शिक्षण अस्पताल"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Address (English)</label>
                <input type="text" name="address" required placeholder="e.g. Maharajgunj, Kathmandu"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Helpline</label>
                <input type="text" name="phone" required placeholder="e.g. 01-4412765"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Hospital Type</label>
            <select name="type" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="Government">Government Hospital</option>
                <option value="Private">Private Hospital</option>
                <option value="Community">Community Health Center</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Latitude</label>
                <input type="text" name="latitude" placeholder="e.g. 27.7371"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Longitude</label>
                <input type="text" name="longitude" placeholder="e.g. 85.3331"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-blue-600 rounded">
                <span class="text-sm font-medium text-gray-700">Active in Emergency List</span>
            </label>
        </div>

        <div class="pt-4 flex gap-3">
            <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                Save Hospital
            </button>
            <a href="{{ route('admin.hospitals.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
