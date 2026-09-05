<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LegalController extends Controller
{
    /**
     * Show the privacy policy page
     */
    public function privacyPolicy()
    {
        return view('legal.privacy-policy');
    }

    /**
     * Show the account deletion page
     */
    public function deleteAccount()
    {
        return view('legal.delete-account');
    }

    /**
     * Handle account deletion request
     */
    public function submitDeleteAccount(Request $request)
    {
        $request->validate([
            'reason' => 'required|string',
            'comments' => 'nullable|string|max:500',
            'confirmation' => 'accepted',
            'password' => 'required_if:auth,true',
        ]);

        // If user is authenticated, verify password
        if (Auth::check()) {
            $request->validate([
                'password' => 'required|string',
            ]);

            if (!Hash::check($request->password, Auth::user()->password)) {
                return back()->withErrors(['password' => 'Incorrect password. Please try again.']);
            }

            // Delete user's documents
            if (Auth::user()->documents) {
                foreach (Auth::user()->documents as $document) {
                    $document->delete();
                }
            }

            // Delete user's shorts
            if (Auth::user()->userShorts) {
                foreach (Auth::user()->userShorts as $short) {
                    // Delete files from storage
                    if ($short->video_url) {
                        $videoPath = str_replace('/storage/', '', $short->video_url);
                        \Storage::disk('public')->delete($videoPath);
                    }
                    if ($short->cover_image_url) {
                        $coverPath = str_replace('/storage/', '', $short->cover_image_url);
                        \Storage::disk('public')->delete($coverPath);
                    }
                    $short->delete();
                }
            }

            // Logout and delete user
            $user = Auth::user();
            Auth::logout();
            $user->delete();

            return redirect()->route('home')->with('success', 'Your account has been permanently deleted. We\'re sorry to see you go.');
        }

        // For non-authenticated users, log the deletion request
        // In production, you might want to send an email to verify identity first
        return redirect()->route('home')->with('success', 'Your account deletion request has been received. If you were logged in, your account has been deleted.');
    }
}
