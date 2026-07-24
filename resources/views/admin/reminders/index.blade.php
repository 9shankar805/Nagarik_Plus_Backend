@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">User Reminders & Expiry Tracking</h1>
        <p class="text-sm text-gray-600">Overview of active citizen reminders for document expirations and renewals.</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">User</th>
                <th class="py-3 px-4">Reminder Title</th>
                <th class="py-3 px-4">Category</th>
                <th class="py-3 px-4">Due Date</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($reminders as $reminder)
            <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4 font-semibold text-gray-900">
                    {{ $reminder->user->name ?? 'User #' . $reminder->user_id }}
                </td>
                <td class="py-3 px-4 text-gray-900 font-medium">
                    {{ $reminder->title }}
                </td>
                <td class="py-3 px-4">
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                        {{ ucfirst($reminder->category ?? 'General') }}
                    </span>
                </td>
                <td class="py-3 px-4 text-gray-600">
                    {{ $reminder->due_date ? \Carbon\Carbon::parse($reminder->due_date)->format('M d, Y') : '-' }}
                </td>
                <td class="py-3 px-4 text-right">
                    <form action="{{ route('admin.reminders.destroy', $reminder) }}" method="POST" class="inline" onsubmit="return confirm('Remove this reminder?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-8 text-center text-gray-500">No user reminders found in the system.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">
        {{ $reminders->links() }}
    </div>
</div>
@endsection
