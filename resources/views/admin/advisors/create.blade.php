@extends('admin.layouts.app')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.advisors.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Advisors</a>
    <h1 class="text-2xl font-bold text-gray-900 mt-2">Add Nagarik Advisor</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form action="{{ route('admin.advisors.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name</label>
            <input type="text" name="name" required placeholder="e.g. Advocate Ramesh Bikram Shah"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Title (English)</label>
                <input type="text" name="title_en" required placeholder="e.g. Senior Public Rights Legal Specialist"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                <select name="category" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="legal">Legal Advisor</option>
                    <option value="tax">Tax & Accounting</option>
                    <option value="consular">Passport & Foreign Consular</option>
                    <option value="property">Land & Property</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Chat Fee (NPR)</label>
                <input type="number" name="consultation_fee_chat" value="250" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Call Fee (NPR)</label>
                <input type="number" name="consultation_fee_call" value="500" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Experience Years</label>
                <input type="number" name="experience_years" value="10" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Location</label>
                <input type="text" name="location" placeholder="e.g. Kathmandu, Nepal"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
        </div>

        <div class="flex gap-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_online" value="1" checked class="w-4 h-4 text-blue-600 rounded">
                <span class="text-sm font-medium text-gray-700">Online Now</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_verified" value="1" checked class="w-4 h-4 text-blue-600 rounded">
                <span class="text-sm font-medium text-gray-700">Verified Badge</span>
            </label>
        </div>

        <div class="pt-4 flex gap-3">
            <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                Save Advisor Profile
            </button>
            <a href="{{ route('admin.advisors.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
