@extends('layouts.user')

@section('title', 'Citizen Services Guides')
@section('subtitle', 'Step-by-step guides for government services')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($guides as $guide)
        @php
            $guideImages = [
                'passport' => 'passport1752476337775.png',
                'pan-card' => 'pan.png',
                'national-id' => 'nid1752476653129.png',
                'driving-license' => 'license1752476621950.png',
                'voter-registration' => 'voterid.png',
                'company-registration' => 'cit1759940267390.png',
            ];
            $imagePath = $guideImages[$guide->slug] ?? 'unnamed.webp';
        @endphp
        <a href="{{ route('user.guides.show', $guide->slug) }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center overflow-hidden" style="background-color: {{ $guide->color }}20;">
                    <img src="{{ asset('assets/images/' . $imagePath) }}" alt="{{ $guide->title }}" class="w-full h-full object-cover">
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">{{ $guide->title }}</h3>
                    <p class="text-xs text-gray-500">{{ $guide->category }}</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 line-clamp-2">{{ $guide->description }}</p>
        </a>
        @endforeach
    </div>
</div>
@endsection
