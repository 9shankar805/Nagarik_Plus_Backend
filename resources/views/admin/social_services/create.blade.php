@extends('admin.layouts.app')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.social-services.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Social Services</a>
    <h1 class="text-2xl font-bold text-gray-900 mt-2">Add Social Service Card</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form action="{{ route('admin.social-services.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title (English)</label>
            <input type="text" name="title_en" required placeholder="e.g. Citizen Investment Trust (CIT)"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title (Nepali)</label>
            <input type="text" name="title_np" placeholder="उदा. नागरिकानी लगानी कोष"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Subtitle (English)</label>
            <input type="text" name="subtitle_en" placeholder="e.g. Check balances and investment statements"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Subtitle (Nepali)</label>
            <input type="text" name="subtitle_np" placeholder="उदा. ब्यालेन्स र लगानी विवरण हेर्नुहोस्"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Icon Key</label>
                <input type="text" name="icon" placeholder="e.g. badge, account_balance"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Hex Color</label>
                <input type="text" name="color" placeholder="e.g. #4A5D4A"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-blue-600 rounded">
                <span class="text-sm font-medium text-gray-700">Active</span>
            </label>
        </div>

        <div class="pt-4 flex gap-3">
            <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                Save Card
            </button>
            <a href="{{ route('admin.social-services.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
