@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Emergency Hospitals</h1>
        <p class="text-sm text-gray-600">Manage hospital contacts and geolocation markers for emergency services.</p>
    </div>
    <a href="{{ route('admin.hospitals.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm flex items-center gap-2">
        <span>+ Add New Hospital</span>
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Hospital Name</th>
                <th class="py-3 px-4">Address</th>
                <th class="py-3 px-4">Phone</th>
                <th class="py-3 px-4">Type</th>
                <th class="py-3 px-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($hospitals as $hospital)
            <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4 font-semibold text-gray-900">
                    <div>{{ $hospital->name }}</div>
                    <div class="text-xs text-gray-500 font-normal">{{ $hospital->name_np }}</div>
                </td>
                <td class="py-3 px-4 text-gray-600">{{ $hospital->address }}</td>
                <td class="py-3 px-4 text-blue-600 font-medium">{{ $hospital->phone }}</td>
                <td class="py-3 px-4">
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                        {{ $hospital->type }}
                    </span>
                </td>
                <td class="py-3 px-4 text-right space-x-2">
                    <a href="{{ route('admin.hospitals.edit', $hospital) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                    <form action="{{ route('admin.hospitals.destroy', $hospital) }}" method="POST" class="inline" onsubmit="return confirm('Delete hospital?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-8 text-center text-gray-500">No hospitals found. Click "+ Add New Hospital" to add one.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">
        {{ $hospitals->links() }}
    </div>
</div>
@endsection
