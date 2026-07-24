@extends('admin.layouts.app')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.advisors.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Advisors</a>
    <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit Advisor Profile #{{ $advisor->id }}</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form action="{{ route('admin.advisors.update', $advisor) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name</label>
            <input type="text" name="name" value="{{ $advisor->name }}" required
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Title (English)</label>
                <input type="text" name="title_en" value="{{ $advisor->title_en }}" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                <select name="category" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="legal" @if($advisor->category === 'legal') selected @endif>Legal Advisor</option>
                    <option value="tax" @if($advisor->category === 'tax') selected @endif>Tax & Accounting</option>
                    <option value="consular" @if($advisor->category === 'consular') selected @endif>Passport & Foreign Consular</option>
                    <option value="property" @if($advisor->category === 'property') selected @endif>Land & Property</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Chat Fee (NPR)</label>
                <input type="number" name="consultation_fee_chat" value="{{ $advisor->consultation_fee_chat }}" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Call Fee (NPR)</label>
                <input type="number" name="consultation_fee_call" value="{{ $advisor->consultation_fee_call }}" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Experience Years</label>
                <input type="number" name="experience_years" value="{{ $advisor->experience_years }}" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Location</label>
                <input type="text" name="location" value="{{ $advisor->location }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
        </div>

        <div class="flex gap-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_online" value="1" @if($advisor->is_online) checked @endif class="w-4 h-4 text-blue-600 rounded">
                <span class="text-sm font-medium text-gray-700">Online Now</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_verified" value="1" @if($advisor->is_verified) checked @endif class="w-4 h-4 text-blue-600 rounded">
                <span class="text-sm font-medium text-gray-700">Verified Badge</span>
            </label>
        </div>

        <div class="pt-4 flex gap-3">
            <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                Update Advisor Profile
            </button>
            <a href="{{ route('admin.advisors.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
