@extends('layouts.public')

@section('title', 'Delete Account')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-slate-900 mb-2">Delete Account</h1>
            <p class="text-slate-600">This action is permanent and cannot be undone.</p>
        </div>

        <!-- Warning Box -->
        <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-8">
            <h3 class="text-lg font-semibold text-red-900 mb-2">Before you proceed, please note:</h3>
            <ul class="list-disc pl-5 space-y-2 text-red-800">
                <li>All your personal information will be permanently deleted from our servers</li>
                <li>All documents stored in your digital locker will be deleted</li>
                <li>Cloud-synced data will be permanently removed</li>
                <li>You will lose access to all government services linked to your account</li>
                <li>This action cannot be reversed</li>
            </ul>
        </div>

        <!-- Deletion Form -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form method="POST" action="{{ route('delete-account.submit') }}">
                @csrf
                
                <!-- Reason for Deletion -->
                <div class="mb-6">
                    <label for="reason" class="block text-sm font-medium text-slate-700 mb-2">
                        Reason for deletion <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="reason" 
                        name="reason" 
                        required
                        class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                    >
                        <option value="">Please select a reason</option>
                        <option value="no_longer_needed">I no longer need the service</option>
                        <option value="privacy_concerns">I have privacy concerns</option>
                        <option value="found_alternative">I found an alternative service</option>
                        <option value="technical_issues">I'm experiencing technical issues</option>
                        <option value="not_satisfied">I'm not satisfied with the service</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Additional Comments -->
                <div class="mb-6">
                    <label for="comments" class="block text-sm font-medium text-slate-700 mb-2">
                        Additional comments (optional)
                    </label>
                    <textarea 
                        id="comments" 
                        name="comments" 
                        rows="4"
                        placeholder="Please let us know how we can improve our service..."
                        class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors resize-none"
                    ></textarea>
                </div>

                <!-- Confirmation Checkbox -->
                <div class="mb-6">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="confirmation" 
                            required
                            class="mt-1 w-5 h-5 text-red-600 border-slate-300 rounded focus:ring-red-500"
                        >
                        <span class="text-sm text-slate-700">
                            I understand that this action is permanent and cannot be undone. I want to permanently delete my Nagarik+ account and all associated data.
                        </span>
                    </label>
                </div>

                <!-- Password Confirmation (if authenticated) -->
                @auth
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                        Confirm your password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        placeholder="Enter your password to confirm"
                        class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                    >
                </div>
                @endauth

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <button 
                        type="submit"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    >
                        Delete My Account
                    </button>
                    <a 
                        href="{{ url('/') }}"
                        class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-800 font-semibold py-3 px-6 rounded-lg transition-colors text-center"
                    >
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Alternative Option -->
        <div class="mt-8 text-center">
            <p class="text-slate-600 mb-4">Not sure about deleting your account?</p>
            <a href="{{ route('user.login') }}" class="text-indigo-600 hover:text-indigo-800 font-medium underline">
                Log in to manage your account settings instead
            </a>
        </div>
    </div>
</div>
@endsection
