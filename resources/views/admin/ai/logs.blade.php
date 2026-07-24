@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">AI Assistant Audit Logs</h1>
        <p class="text-sm text-gray-600">Review AI assistant chat interactions and response quality.</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">User</th>
                <th class="py-3 px-4">User Prompt</th>
                <th class="py-3 px-4">AI Response</th>
                <th class="py-3 px-4">Timestamp</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($conversations as $convo)
            <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4 font-semibold text-gray-900">
                    {{ $convo->user->name ?? 'Anonymous User' }}
                </td>
                <td class="py-3 px-4 text-gray-800 font-medium">
                    {{ Str::limit($convo->user_message ?? $convo->prompt, 60) }}
                </td>
                <td class="py-3 px-4 text-gray-600">
                    {{ Str::limit($convo->ai_response ?? $convo->response, 80) }}
                </td>
                <td class="py-3 px-4 text-gray-500 text-xs">
                    {{ $convo->created_at ? $convo->created_at->diffForHumans() : 'Just now' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="py-8 text-center text-gray-500">No AI conversations logged yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">
        {{ $conversations->links() }}
    </div>
</div>
@endsection
