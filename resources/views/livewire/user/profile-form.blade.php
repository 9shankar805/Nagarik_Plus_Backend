<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Profile & Settings</h1>

    @if(session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 md:grid-cols-2">
        <!-- Profile Info -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Profile Information</h2>
            <form wire:submit="saveProfile">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input wire:model="name" type="text" class="w-full border rounded-lg px-3 py-2">
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input wire:model="email" type="email" class="w-full border rounded-lg px-3 py-2">
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input wire:model="phone" type="text" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                    <input wire:model="profile_photo" type="file" class="w-full border rounded-lg px-3 py-2">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Save Changes
                </button>
            </form>
        </div>

        <!-- Change Password -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Change Password</h2>
            <form wire:submit="changePassword">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                    <input wire:model="current_password" type="password" class="w-full border rounded-lg px-3 py-2">
                    @error('current_password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input wire:model="new_password" type="password" class="w-full border rounded-lg px-3 py-2">
                    @error('new_password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <input wire:model="new_password_confirmation" type="password" class="w-full border rounded-lg px-3 py-2">
                </div>
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg">
                    Change Password
                </button>
            </form>
        </div>

        <!-- Notification Preferences -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Notification Preferences</h2>
            <form wire:submit="savePreferences">
                <div class="space-y-3">
                    <label class="flex items-center gap-3">
                        <input wire:model="notification_preferences.document_reminders" type="checkbox" class="rounded">
                        <span class="text-sm text-gray-700">Document Reminders</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input wire:model="notification_preferences.news_updates" type="checkbox" class="rounded">
                        <span class="text-sm text-gray-700">News Updates</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input wire:model="notification_preferences.system_announcements" type="checkbox" class="rounded">
                        <span class="text-sm text-gray-700">System Announcements</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input wire:model="notification_preferences.learning_updates" type="checkbox" class="rounded">
                        <span class="text-sm text-gray-700">Learning Updates</span>
                    </label>
                </div>
                <button type="submit" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Save Preferences
                </button>
            </form>
        </div>

        <!-- Active Sessions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Active Sessions</h2>
            <div class="space-y-3">
                @if($tokens->isEmpty())
                <p class="text-sm text-gray-500">No active sessions found.</p>
                @else
                @foreach($tokens as $token)
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div>
                        <div class="text-sm font-medium text-gray-800">{{ $token->name }}</div>
                        <div class="text-xs text-gray-500">
                            Last used: {{ $token->last_used_at ? $token->last_used_at->diffForHumans() : 'Never' }}
                        </div>
                    </div>
                    <button wire:click="revokeToken({{ $token->id }})"
                            class="text-red-600 hover:text-red-800 text-sm">
                        Revoke
                    </button>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
