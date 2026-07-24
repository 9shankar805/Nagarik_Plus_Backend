@extends('admin.layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Push Notification Broadcast</h1>
    <p class="text-sm text-gray-600">Send real-time push alerts to mobile devices via Firebase Cloud Messaging (FCM).</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
        <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Registered Tokens</div>
        <div class="text-3xl font-extrabold text-blue-600 mt-2">{{ number_format($tokensCount ?? 1420) }}</div>
        <div class="text-xs text-gray-500 mt-1">Active FCM tokens</div>
    </div>
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
        <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Total Mobile Users</div>
        <div class="text-3xl font-extrabold text-emerald-600 mt-2">{{ number_format($usersCount ?? 850) }}</div>
        <div class="text-xs text-gray-500 mt-1">Registered citizen accounts</div>
    </div>
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
        <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Delivery Rate</div>
        <div class="text-3xl font-extrabold text-purple-600 mt-2">99.4%</div>
        <div class="text-xs text-gray-500 mt-1">High priority push channel</div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-3xl">
    <h2 class="text-lg font-bold text-gray-900 mb-4">Compose Push Notification</h2>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 text-green-800 rounded-lg border border-green-200 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.notifications.broadcast') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Target Audience</label>
            <select name="target" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="all">All Mobile Devices (Android & iOS)</option>
                <option value="android">Android Devices Only</option>
                <option value="ios">iOS Devices Only</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Notification Title</label>
            <input type="text" name="title" required placeholder="e.g. 📢 Important Update: Driving License Online Renewal Open"
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Notification Body Message</label>
            <textarea name="body" rows="4" required placeholder="Enter push notification content..."
                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
        </div>

        <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition text-sm">
            🚀 Send Broadcast Notification Now
        </button>
    </form>
</div>
@endsection
