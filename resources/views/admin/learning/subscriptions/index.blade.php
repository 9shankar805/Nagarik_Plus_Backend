@extends('admin.layouts.app')

@section('title', 'Subscription Packages')
@section('subtitle', 'Manage premium access plans for students')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Subscription Packages</h2>
        <p class="text-sm text-gray-500 mt-1">Define plans that unlock access to mock tests, video classes and more.</p>
    </div>
    <a href="{{ route('admin.learning.subscriptions.create') }}"
       class="px-4 py-2 bg-[#4A5D4A] text-white rounded-lg hover:bg-[#3F523F] transition font-medium text-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Package
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
    @forelse($packages as $pkg)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col gap-3">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">{{ $pkg->name }}</h3>
                <p class="text-sm text-gray-500">{{ $pkg->duration_days }} days</p>
            </div>
            @if($pkg->is_active)
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
            @else
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-500">Inactive</span>
            @endif
        </div>

        <div class="text-3xl font-bold text-[#4A5D4A]">
            Rs {{ number_format($pkg->price) }}
            <span class="text-sm font-normal text-gray-500">/ {{ $pkg->duration_days }}d</span>
        </div>

        @if($pkg->features_json)
        <ul class="space-y-1">
            @foreach($pkg->features_json as $feature)
            <li class="flex items-center gap-2 text-sm text-gray-700">
                <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ $feature }}
            </li>
            @endforeach
        </ul>
        @endif

        <div class="flex items-center justify-between pt-2 border-t border-gray-100 mt-auto">
            <span class="text-xs text-gray-500">{{ $pkg->user_subscriptions_count }} subscribers</span>
            <div class="flex gap-3">
                <a href="{{ route('admin.learning.subscriptions.show', $pkg) }}"
                   class="text-xs text-gray-600 hover:text-gray-900 font-medium">Subscribers</a>
                <a href="{{ route('admin.learning.subscriptions.edit', $pkg) }}"
                   class="text-xs text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                <form action="{{ route('admin.learning.subscriptions.destroy', $pkg) }}" method="POST"
                      class="inline" onsubmit="return confirm('Delete this package?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-medium">Delete</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-3 py-12 text-center text-gray-400">
        No subscription packages yet.
        <a href="{{ route('admin.learning.subscriptions.create') }}" class="text-blue-600 hover:underline ml-1">Create one</a>.
    </div>
    @endforelse
</div>

<div>{{ $packages->links() }}</div>
@endsection
