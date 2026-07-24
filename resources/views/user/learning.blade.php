@extends('layouts.user')

@section('title', 'Learning Center')
@section('subtitle', 'Learn about road signs and practice driving tests')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Road Signs --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Road Signs</h2>
                    <p class="text-sm text-gray-500">Learn about all traffic signs</p>
                </div>
            </div>
            <p class="text-gray-600 mb-6">Explore and study all road signs with detailed explanations to help you prepare for your driving test.</p>
            <button class="w-full bg-yellow-600 hover:bg-yellow-700 text-white py-3 rounded-lg font-medium">
                Browse Road Signs
            </button>
        </div>

        {{-- Mock Tests --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Mock Tests</h2>
                    <p class="text-sm text-gray-500">Practice with sample questions</p>
                </div>
            </div>
            <p class="text-gray-600 mb-6">Test your knowledge with our mock driving tests featuring real questions and instant feedback.</p>
            <button class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-medium">
                Start a Test
            </button>
        </div>
    </div>
</div>
@endsection
