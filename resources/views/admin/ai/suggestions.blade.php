@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">AI Prompt Suggestion Chips</h1>
        <p class="text-sm text-gray-600">Manage quick starter prompts shown in the Nagarik AI Assistant mobile interface.</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">#</th>
                <th class="py-3 px-4">Suggestion Text</th>
                <th class="py-3 px-4">Category</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($suggestions as $chip)
            <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4 font-mono text-gray-500">{{ $chip['id'] }}</td>
                <td class="py-3 px-4 font-semibold text-gray-900">{{ $chip['text'] }}</td>
                <td class="py-3 px-4">
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                        {{ $chip['category'] }}
                    </span>
                </td>
                <td class="py-3 px-4 text-right">
                    <button class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="py-8 text-center text-gray-500">No prompt suggestions found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
