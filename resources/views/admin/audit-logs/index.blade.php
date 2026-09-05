@extends('admin.layouts.app')

@section('title', 'Audit Logs')
@section('subtitle', 'Track system changes and admin actions')

@section('content')

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-4 font-medium">Date</th>
                    <th class="px-6 py-4 font-medium">Actor</th>
                    <th class="px-6 py-4 font-medium">Action</th>
                    <th class="px-6 py-4 font-medium">Subject</th>
                    <th class="px-6 py-4 font-medium text-right">Details</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 text-gray-500 text-xs">{{ $log->created_at->format('d M Y, H:i:s') }}</td>
                        <td class="px-6 py-4">
                            @if($log->causer)
                                <a href="{{ route('admin.users.show', $log->causer_id) }}" class="text-blue-600 font-medium hover:underline">{{ $log->causer->name }}</a>
                            @else
                                <span class="text-gray-400 italic">System</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($log->event === 'created')
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded font-semibold">Created</span>
                            @elseif($log->event === 'updated')
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded font-semibold">Updated</span>
                            @elseif($log->event === 'deleted')
                                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded font-semibold">Deleted</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded font-semibold">{{ ucfirst($log->event) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-900 font-medium text-xs">
                            {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.audit-logs.show', $log) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            No audit logs found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
    @endif
</div>

@endsection
