@extends('admin.layouts.app')

@section('title', 'Users')
@section('subtitle', 'Manage registered users')

@section('content')

{{-- Search --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by name, email or phone…"
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="submit"
                class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
            Search
        </button>
        @if(request('search'))
            <a href="{{ route('admin.users.index') }}"
               class="px-5 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                Clear
            </a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <p class="text-sm text-gray-600">
            Showing <span class="font-semibold">{{ $users->firstItem() }}–{{ $users->lastItem() }}</span>
            of <span class="font-semibold">{{ $users->total() }}</span> users
        </p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Docs</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Reminders</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $user->phone ?? '—' }}</td>
                        <td class="px-6 py-3 text-center text-gray-600">{{ $user->documents_count }}</td>
                        <td class="px-6 py-3 text-center text-gray-600">{{ $user->reminders_count }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-3 text-center">
                            @if($user->isBanned())
                                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">Banned</span>
                            @elseif($user->is_active)
                                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Active</span>
                            @else
                                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="px-3 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 border border-blue-200 hover:border-blue-400 rounded-lg transition-colors">
                                    View
                                </a>
                                @if(!$user->isBanned())
                                <form method="POST" action="{{ route('admin.users.ban', $user) }}"
                                      onsubmit="return confirm('Ban this user?')" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-1 text-xs font-medium text-orange-600 hover:text-orange-800 border border-orange-200 hover:border-orange-400 rounded-lg transition-colors">
                                        Ban
                                    </button>
                                </form>
                                @endif
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm('Deactivate this user?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1 text-xs font-medium text-red-600 hover:text-red-800 border border-red-200 hover:border-red-400 rounded-lg transition-colors">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
    @endif
</div>

@endsection
