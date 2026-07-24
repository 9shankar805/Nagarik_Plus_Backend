@extends('layouts.auth')

@section('title', 'Login — Nagarik+')

@section('content')
<div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
    {{-- Header --}}
    <div class="px-8 py-8 text-center" style="background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);">
        <img src="/icon.png" alt="Nagarik+" class="w-16 h-16 rounded-2xl mx-auto mb-4 shadow-lg object-cover">
        <h1 class="text-2xl font-bold text-white">Welcome back!</h1>
        <p class="text-blue-200 text-sm mt-1">Sign in to your Nagarik+ account</p>
    </div>

    {{-- Form --}}
    <div class="px-8 py-8">
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm mb-6">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('user.login.post') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition
                              {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                       placeholder="you@example.com">
                @error('email')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                       placeholder="••••••••">
                @error('password')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember"
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="remember" class="ml-2 text-sm text-gray-600">Keep me signed in</label>
                </div>
            </div>

            <button type="submit"
                    class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Sign In
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-600">
            Don't have an account?
            <a href="{{ route('user.register') }}" class="font-semibold text-blue-600 hover:text-blue-700">Sign up</a>
        </div>
    </div>
</div>
@endsection