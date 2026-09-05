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

<!-- Leaflet CSS for Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-3">Hospitals Map Overview</h2>
    <div id="hospitalsMap" class="w-full h-[400px] rounded-lg border border-gray-200 z-0 relative"></div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="py-3 px-4">Hospital Name</th>
                <th class="py-3 px-4">Address</th>
                <th class="py-3 px-4">Phone</th>
                <th class="py-3 px-4 text-center">Location</th>
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
                <td class="py-3 px-4">
                    @if($hospital->phone)
                        <span class="text-blue-600 font-medium">{{ $hospital->phone }}</span>
                    @else
                        <span class="text-xs text-red-500 font-medium">Missing</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-center">
                    @if($hospital->latitude && $hospital->longitude)
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-50 text-green-700 border border-green-200" title="{{ $hospital->latitude }}, {{ $hospital->longitude }}">
                            Mapped
                        </span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-50 text-red-700 border border-red-200">
                            Unmapped
                        </span>
                    @endif
                </td>
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

<!-- Leaflet JS for Map -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map centered roughly on Nepal
        const map = L.map('hospitalsMap').setView([27.7, 85.3], 7);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Load hospitals from PHP to JS
        const hospitals = @json($hospitals->items());
        const bounds = [];

        hospitals.forEach(hospital => {
            if (hospital.latitude && hospital.longitude) {
                const marker = L.marker([hospital.latitude, hospital.longitude]).addTo(map);
                
                const popupContent = `
                    <div class="text-sm">
                        <strong class="text-indigo-600 text-base">${hospital.name}</strong><br>
                        ${hospital.address ? hospital.address + '<br>' : ''}
                        ${hospital.phone ? '📞 ' + hospital.phone : ''}
                    </div>
                `;
                marker.bindPopup(popupContent);
                bounds.push([hospital.latitude, hospital.longitude]);
            }
        });

        // Auto-zoom map to fit all markers if any exist
        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [50, 50], maxZoom: 14 });
        }
    });
</script>

@endsection
