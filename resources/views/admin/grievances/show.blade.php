@extends('admin.layouts.app')

@section('title', 'Ticket #' . $grievance->id)
@section('subtitle', 'Grievance Review')

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.grievances.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Grievances</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left: Details & Map --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $grievance->title }}</h2>
                    <p class="text-sm text-gray-500 mt-1">Reported by <a href="{{ route('admin.users.show', $grievance->user_id) }}" class="text-blue-600 hover:underline">{{ $grievance->user->name ?? 'Unknown' }}</a> on {{ $grievance->created_at->format('d M Y, h:i A') }}</p>
                </div>
                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">{{ $grievance->category->name_en ?? 'N/A' }}</span>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg text-gray-800 text-sm mb-6 border border-gray-100 whitespace-pre-wrap">{{ $grievance->description }}</div>

            @if($grievance->photo_path)
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Attached Evidence</h3>
                    <a href="{{ Storage::url($grievance->photo_path) }}" target="_blank">
                        <img src="{{ Storage::url($grievance->photo_path) }}" alt="Grievance Photo" class="w-full max-w-md h-auto rounded-lg border shadow-sm">
                    </a>
                </div>
            @endif

            @if($grievance->latitude && $grievance->longitude)
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Location</h3>
                    <div id="map" class="w-full h-64 rounded-lg border shadow-sm"></div>
                </div>
            @endif
        </div>
    </div>

    {{-- Right: Status Updater --}}
    <div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-6">
            <h3 class="font-bold text-gray-900 mb-4 pb-2 border-b">Status & Action</h3>
            
            <form action="{{ route('admin.grievances.update', $grievance) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Status</label>
                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="open" {{ $grievance->status === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ $grievance->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ $grievance->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="rejected" {{ $grievance->status === 'rejected' ? 'selected' : '' }}>Rejected (Invalid)</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Admin Response</label>
                    <textarea name="admin_response" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm" placeholder="Message to citizen...">{{ old('admin_response', $grievance->admin_response) }}</textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    Update Ticket
                </button>
            </form>

            @if($grievance->resolved_by)
                <div class="mt-6 pt-4 border-t border-gray-100 text-sm text-gray-500">
                    Last closed/rejected by: <strong>{{ $grievance->resolver->name ?? 'Unknown' }}</strong>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
@if($grievance->latitude && $grievance->longitude)
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var lat = {{ $grievance->latitude }};
            var lng = {{ $grievance->longitude }};
            var map = L.map('map').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            L.marker([lat, lng]).addTo(map).bindPopup("Reported Location");
        });
    </script>
@endif
@endpush
