@extends('admin.layouts.app')

@section('title', 'Edit Subscription Package')
@section('subtitle', 'Update package details and features')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.learning.subscriptions.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Packages</a>
    <h2 class="text-2xl font-bold text-gray-900 mt-2">Edit: {{ $subscription->name }}</h2>
</div>

<form action="{{ route('admin.learning.subscriptions.update', $subscription) }}" method="POST">
    @csrf @method('PUT')
    <div class="grid grid-cols-3 gap-6">

        <div class="col-span-2 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Package Details</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Package Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $subscription->name) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] @error('name') border-red-400 @enderror">
                        @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Price (Rs) <span class="text-red-500">*</span></label>
                        <input type="number" name="price" value="{{ old('price', $subscription->price) }}" required min="0" step="0.01"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Duration (days) <span class="text-red-500">*</span></label>
                    <input type="number" name="duration_days" value="{{ old('duration_days', $subscription->duration_days) }}" min="1" required
                           class="w-48 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A] @error('duration_days') border-red-400 @enderror">
                    @error('duration_days')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Features / Benefits</h3>

                <div id="features-list" class="space-y-2">
                    @foreach(old('features', $subscription->features_json ?? []) as $feat)
                    <div class="flex gap-2 items-center feature-row">
                        <input type="text" name="features[]" value="{{ $feat }}"
                               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                        <button type="button" onclick="this.closest('.feature-row').remove()"
                                class="text-red-400 hover:text-red-600 text-xl leading-none">&times;</button>
                    </div>
                    @endforeach
                </div>
                <button type="button" onclick="addFeature()"
                        class="text-sm text-[#4A5D4A] font-medium hover:underline">+ Add Feature</button>
            </div>
        </div>

        <div class="col-span-1 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
                <h3 class="font-semibold text-gray-800 border-b pb-2">Settings</h3>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $subscription->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-[#4A5D4A] rounded border-gray-300">
                    <span class="text-sm font-medium text-gray-700">Active (visible to users)</span>
                </label>
            </div>

            <div class="flex flex-col gap-3">
                <button type="submit"
                        class="w-full px-5 py-2.5 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] transition font-medium text-sm">
                    Save Changes
                </button>
                <a href="{{ route('admin.learning.subscriptions.index') }}"
                   class="w-full text-center px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</form>

<script>
function addFeature() {
    const list = document.getElementById('features-list');
    const row  = document.createElement('div');
    row.className = 'flex gap-2 items-center feature-row';
    row.innerHTML = `<input type="text" name="features[]" placeholder="e.g. Access all video classes"
                            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#4A5D4A]">
                     <button type="button" onclick="this.closest('.feature-row').remove()"
                             class="text-red-400 hover:text-red-600 text-xl leading-none">&times;</button>`;
    list.appendChild(row);
    row.querySelector('input').focus();
}
</script>
@endsection
