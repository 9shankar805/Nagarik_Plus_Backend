@extends('admin.layouts.app')

@section('title', 'Audit Log Details')
@section('subtitle', 'Log #' . $activity->id)

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.audit-logs.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Logs</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-gray-900 mb-4 pb-2 border-b">Log Information</h3>
        
        <div class="space-y-4 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Event</span>
                <span class="font-bold text-gray-900 uppercase">{{ $activity->event }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Date</span>
                <span class="font-medium text-gray-800">{{ $activity->created_at->format('d M Y, h:i:s A') }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Causer</span>
                @if($activity->causer)
                    <a href="{{ route('admin.users.show', $activity->causer_id) }}" class="font-medium text-blue-600 hover:underline">{{ $activity->causer->name }}</a>
                @else
                    <span class="font-medium text-gray-400 italic">System</span>
                @endif
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Subject Type</span>
                <span class="font-medium text-gray-800">{{ $activity->subject_type ?? 'N/A' }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Subject ID</span>
                <span class="font-medium text-gray-800">{{ $activity->subject_id ?? 'N/A' }}</span>
            </div>
            
            @if($activity->description)
            <div class="flex flex-col">
                <span class="text-gray-500 mb-1">Description</span>
                <span class="font-medium text-gray-800">{{ $activity->description }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-gray-900 mb-4 pb-2 border-b">Changes</h3>
        
        @if($activity->properties && $activity->properties->count() > 0)
            <div class="space-y-6">
                @if(isset($activity->properties['old']))
                    <div>
                        <h4 class="text-xs font-semibold text-red-600 uppercase mb-2">Old Attributes</h4>
                        <pre class="bg-red-50 text-red-900 p-4 rounded-lg text-xs overflow-x-auto"><code>{{ json_encode($activity->properties['old'], JSON_PRETTY_PRINT) }}</code></pre>
                    </div>
                @endif
                
                @if(isset($activity->properties['attributes']))
                    <div>
                        <h4 class="text-xs font-semibold text-green-600 uppercase mb-2">New Attributes</h4>
                        <pre class="bg-green-50 text-green-900 p-4 rounded-lg text-xs overflow-x-auto"><code>{{ json_encode($activity->properties['attributes'], JSON_PRETTY_PRINT) }}</code></pre>
                    </div>
                @endif
            </div>
        @else
            <p class="text-gray-500 text-sm">No properties or changes recorded for this event.</p>
        @endif
    </div>
</div>

@endsection
