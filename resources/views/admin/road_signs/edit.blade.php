@extends('admin.layouts.app')

@section('title', 'Edit Road Sign')

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.road-signs.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Road Signs</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 max-w-3xl">
    <form method="POST" action="{{ route('admin.road-signs.update', $roadSign) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Name (English) <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $roadSign->name) }}" required
                       class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('name') ? 'border-red-400' : 'border-gray-300' }}">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Name (Nepali)</label>
                <input type="text" name="name_np" value="{{ old('name_np', $roadSign->name_np) }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Meaning (English) <span class="text-red-500">*</span></label>
                <textarea name="meaning" rows="3" required
                          class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('meaning') ? 'border-red-400' : 'border-gray-300' }}">{{ old('meaning', $roadSign->meaning) }}</textarea>
                @error('meaning') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Meaning (Nepali)</label>
                <textarea name="meaning_np" rows="3"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('meaning_np', $roadSign->meaning_np) }}</textarea>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Category <span class="text-red-500">*</span></label>
                <select name="category" required
                        class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white {{ $errors->has('category') ? 'border-red-400' : 'border-gray-300' }}">
                    @foreach(['warning', 'mandatory', 'informational'] as $cat)
                        <option value="{{ $cat }}" {{ old('category', $roadSign->category) === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                    @endforeach
                </select>
                @error('category') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Color Code</label>
                <input type="text" name="color_code" value="{{ old('color_code', $roadSign->color_code) }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Image URL</label>
                <input type="url" name="image_url" value="{{ old('image_url', $roadSign->image_url) }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $roadSign->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <span class="text-sm font-medium text-gray-700">Active</span>
            </label>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
                Update Road Sign
            </button>
            <a href="{{ route('admin.road-signs.index') }}"
               class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection
