<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileForm extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $phone;
    public $current_password;
    public $new_password;
    public $new_password_confirmation;
    public $profile_photo;
    public $notification_preferences = [];

    public function mount()
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->notification_preferences = $user->notification_preferences ?? [
            'document_reminders'   => true,
            'news_updates'         => true,
            'system_announcements' => true,
            'learning_updates'     => true,
        ];
    }

    public function saveProfile()
    {
        $user = auth()->user();
        $this->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:15|unique:users,phone,' . $user->id,
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ];

        if ($this->profile_photo) {
            // Store and update profile photo
            $path = $this->profile_photo->store('profile-photos', 'public');
            $data['profile_photo'] = $path;
        }

        $user->update($data);

        session()->flash('success', 'Profile updated successfully!');
    }

    public function changePassword()
    {
        $user = auth()->user();
        $this->validate([
            'current_password' => 'required',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The current password is incorrect.');
            return;
        }

        $user->update(['password' => Hash::make($this->new_password)]);

        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        session()->flash('success', 'Password changed successfully!');
    }

    public function savePreferences()
    {
        $user = auth()->user();
        $user->update(['notification_preferences' => $this->notification_preferences]);
        session()->flash('success', 'Notification preferences saved!');
    }

    public function revokeToken($tokenId)
    {
        $user = auth()->user();
        $user->tokens()->where('id', $tokenId)->delete();
        session()->flash('success', 'Token revoked!');
    }

    public function render()
    {
        $user = auth()->user();
        $tokens = $user->tokens()->orderBy('last_used_at', 'desc')->get();

        return view('livewire.user.profile-form', [
            'tokens' => $tokens,
        ]);
    }
}
