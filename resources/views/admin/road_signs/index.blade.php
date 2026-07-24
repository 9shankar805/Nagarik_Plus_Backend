@extends('admin.layouts.app')

@section('title', 'Road Signs')
@section('subtitle', 'Manage road sign reference data')

@section('content')

<div class="flex justify-end mb-6">
    <a href="{{ route('admin.road-signs.create') }}"
       class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
        + New Road Sign
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($signs as $sign)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3">
                            <p class="font-medium text-gray-800">{{ $sign->name }}</p>
                            @if($sign->name_np)
                                <p class="text-xs text-gray-400">{{ $sign->name_np }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full
                                {{ $sign->category === 'warning' ? 'bg-yellow-100 text-yellow-700' :
                                   ($sign->category === 'mandatory' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                                {{ ucfirst($sign->category) }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            @if($sign->color_code)
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full border border-gray-200 inline-block"
                                          style="background-color: {{ $sign->color_code }}"></span>
                                    <span class="text-gray-600 font-mono text-xs">{{ $sign->color_code }}</span>
                                </div>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-center">
                            @if($sign->is_active)
                                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Yes</span>
                            @else
                                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-500">No</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.road-signs.edit', $sign) }}"
                                   class="px-3 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 border border-blue-200 hover:border-blue-400 rounded-lg transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.road-signs.destroy', $sign) }}"
                                      onsubmit="return confirm('Delete this road sign?')" class="inline">
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
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">No road signs yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($signs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $signs->links() }}
        </div>
    @endif
</div>

@endsection
