<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount(['documents', 'reminders']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('kyc_status')) {
            $query->where('kyc_status', $request->kyc_status);
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,learning_admin,user',
            'phone' => 'nullable|string|max:20',
        ]);

        $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        $validated['is_active'] = true;
        $validated['kyc_status'] = 'verified'; // Admins don't need KYC usually

        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'Staff member created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['documents', 'reminders']);
        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'User has been deactivated.');
    }

    public function ban(User $user)
    {
        $user->update(['banned_at' => now()]);
        $user->tokens()->delete();
        return back()->with('success', 'User has been banned.');
    }

    public function kycReview(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string',
        ]);

        $user->update([
            'kyc_status' => $request->status,
            'kyc_rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
            'kyc_verified_at' => $request->status === 'verified' ? now() : null,
        ]);

        return back()->with('success', 'User KYC status updated.');
    }
}
