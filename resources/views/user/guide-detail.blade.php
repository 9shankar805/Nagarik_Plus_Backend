@extends('layouts.user')

@section('title', $guide->title)
@section('subtitle', $guide->category)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
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
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 rounded-lg flex items-center justify-center overflow-hidden" style="background-color: {{ $guide->color }}20;">
                <img src="{{ asset('assets/images/' . $imagePath) }}" alt="{{ $guide->title }}" class="w-full h-full object-cover">
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $guide->title }}</h1>
                <p class="text-sm text-gray-500">{{ $guide->title_np }}</p>
            </div>
        </div>

        <div class="prose max-w-none">
            <p class="text-gray-700 mb-6">{{ $guide->description }}</p>
            @if($guide->description_np)
            <p class="text-gray-700 mb-6">{{ $guide->description_np }}</p>
            @endif

            @if($guide->eligibility)
            <h2 class="text-lg font-semibold text-gray-800 mt-8 mb-4">Eligibility</h2>
            <p class="text-gray-700">{{ $guide->eligibility }}</p>
            @endif

            @if($guide->required_documents)
            <h2 class="text-lg font-semibold text-gray-800 mt-8 mb-4">Required Documents</h2>
            <ul class="list-disc list-inside text-gray-700 space-y-2">
                @foreach($guide->required_documents as $doc)
                <li>{{ $doc }}</li>
                @endforeach
            </ul>
            @endif

            @if($guide->application_steps)
            <h2 class="text-lg font-semibold text-gray-800 mt-8 mb-4">Application Steps</h2>
            <ol class="list-decimal list-inside text-gray-700 space-y-2">
                @foreach($guide->application_steps as $step)
                <li>{{ $step }}</li>
                @endforeach
            </ol>
            @endif

            @if($guide->fee)
            <h2 class="text-lg font-semibold text-gray-800 mt-8 mb-4">Fee</h2>
            <p class="text-gray-700">{{ $guide->fee }}</p>
            @endif

            @if($guide->processing_time)
            <h2 class="text-lg font-semibold text-gray-800 mt-8 mb-4">Processing Time</h2>
            <p class="text-gray-700">{{ $guide->processing_time }}</p>
            @endif

            @if($guide->faqs)
            <h2 class="text-lg font-semibold text-gray-800 mt-8 mb-4">FAQs</h2>
            @foreach($guide->faqs as $faq)
            <div class="mb-4">
                <h3 class="font-medium text-gray-800">{{ $faq['q'] ?? $faq['question'] }}</h3>
                <p class="text-gray-600">{{ $faq['a'] ?? $faq['answer'] }}</p>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
