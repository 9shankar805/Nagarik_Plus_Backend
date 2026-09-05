@extends('admin.layouts.app')

@section('title', 'Settings')
@section('subtitle', 'Application configuration & feature flags')

@section('content')

<div class="max-w-3xl space-y-6">

    {{-- App Info --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Application Info</h2>
        </div>
        <div class="px-6 py-4 space-y-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="text-gray-500">App Name</div>
                <div class="font-medium text-gray-800">{{ $settings['app_name'] }}</div>

                <div class="text-gray-500">Environment</div>
                <div>
                    <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full
                        {{ $settings['app_env'] === 'production' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ ucfirst($settings['app_env']) }}
                    </span>
                </div>

                <div class="text-gray-500">Debug Mode</div>
                <div>
                    @if($settings['app_debug'])
                        <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">Enabled</span>
                    @else
                        <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Disabled</span>
                    @endif
                </div>

                <div class="text-gray-500">Laravel Version</div>
                <div class="font-medium text-gray-800">{{ app()->version() }}</div>

                <div class="text-gray-500">PHP Version</div>
                <div class="font-medium text-gray-800">{{ phpversion() }}</div>
            </div>
        </div>
    </div>

    {{-- Application Settings & Feature Flags --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Application Settings & Feature Flags</h2>
        </div>
        <form method="POST" action="{{ route('admin.settings.update') }}" class="px-6 py-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Max Upload Size (MB)</label>
                <input type="number" name="max_upload_mb" value="{{ $settings['max_upload_mb'] }}" min="1" max="100"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="maintenance_mode" value="1" @if($settings['maintenance_mode']) checked @endif
                           class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Maintenance Mode</span>
                </label>
                <p class="text-xs text-gray-400 mt-1">Enable to put the app in maintenance mode (bypasses admin routes).</p>
            </div>

            <div class="pt-4 border-t border-gray-100 space-y-3">
                <h3 class="text-sm font-bold text-gray-800">App Feature Flags</h3>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" checked disabled class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-gray-700 font-medium">Digital Locker Sync Endpoint</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" checked disabled class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-gray-700 font-medium">Nagarik AI Assistant Endpoint</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" checked disabled class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-gray-700 font-medium">FCM Push Broadcasting</span>
                </label>
            </div>

            <div class="pt-4 border-t border-gray-100 space-y-3">
                <h3 class="text-sm font-bold text-gray-800">Static Support Content (App Profile)</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Help & FAQ</label>
                    <textarea name="help_faq" rows="4" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('help_faq', $settings['help_faq']) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Privacy Policy</label>
                    <textarea name="privacy_policy" rows="4" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('privacy_policy', $settings['privacy_policy']) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">About App</label>
                    <textarea name="about_app" rows="4" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('about_app', $settings['about_app']) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Rate App URL (App Store / Play Store link)</label>
                    <input type="url" name="rate_app_url" value="{{ old('rate_app_url', $settings['rate_app_url']) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://play.google.com/store/apps/details?id=...">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    {{-- Push Notifications --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-gray-800">Push Notifications (FCM)</h2>
                <p class="text-xs text-gray-400 mt-0.5">Firebase Cloud Messaging configuration & Broadcasting</p>
            </div>
            <a href="{{ route('admin.notifications.index') }}" class="px-4 py-2 bg-blue-50 text-blue-700 text-xs font-semibold rounded-lg hover:bg-blue-100 transition">
                Send Push Notification →
            </a>
        </div>
        <div class="px-6 py-6 text-sm text-gray-600">
            <p>FCM configuration is managed via .env file for security. Click "Send Push Notification" above to broadcast real-time alerts to mobile apps.</p>
        </div>
    </div>

</div>

@endsection
