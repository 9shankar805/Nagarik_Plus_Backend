@extends('admin.layouts.app')

@section('title', 'Emergency Contacts')
@section('subtitle', 'Manage emergency service numbers')

@section('content')

<div class="flex justify-end mb-6">
    <a href="{{ route('admin.emergency.create') }}"
       class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
        + New Contact
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Number</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sort</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($contacts as $contact)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3">
                            <p class="font-medium text-gray-800">{{ $contact->name }}</p>
                            @if($contact->name_np)
                                <p class="text-xs text-gray-400">{{ $contact->name_np }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            <span class="font-semibold text-gray-800 text-base">{{ $contact->number }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full
                                {{ $contact->category === 'police' ? 'bg-blue-100 text-blue-700' :
                                   ($contact->category === 'ambulance' ? 'bg-red-100 text-red-700' :
                                   ($contact->category === 'fire' ? 'bg-orange-100 text-orange-700' :
                                   ($contact->category === 'disaster' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700'))) }}">
                                {{ ucfirst($contact->category) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-center text-gray-600">{{ $contact->sort_order }}</td>
                        <td class="px-6 py-3 text-center">
                            @if($contact->is_active)
                                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Yes</span>
                            @else
                                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-500">No</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.emergency.edit', $contact) }}"
                                   class="px-3 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 border border-blue-200 hover:border-blue-400 rounded-lg transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.emergency.destroy', $contact) }}"
                                      onsubmit="return confirm('Delete this contact?')" class="inline">
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
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">No emergency contacts yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($contacts->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $contacts->links() }}
        </div>
    @endif
</div>

@endsection
