<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OtpController extends Controller
{
    /**
     * Send OTP to phone or email.
     */
    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'contact' => 'required|string',
            'type'    => 'required|in:phone,email',
        ]);

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $key = 'otp_' . md5($data['contact']);
        Cache::put($key, Hash::make($otp), now()->addMinutes(10));

        // In production: send via SMS/email. For now log it.
        Log::info("OTP for {$data['contact']}: {$otp}");

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully.',
            'dev_otp' => config('app.debug') ? $otp : null,
        ]);
    }

    /**
     * Verify OTP.
     */
    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'contact' => 'required|string',
            'otp'     => 'required|string|size:6',
        ]);

        $key    = 'otp_' . md5($data['contact']);
        $hashed = Cache::get($key);

        if (!$hashed || !Hash::check($data['otp'], $hashed)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
            ], 422);
        }

        Cache::forget($key);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified.',
        ]);
    }

    /**
     * Initiate forgot-PIN flow — sends a reset OTP.
     */
    public function forgotPin(Request $request): JsonResponse
    {
        $data = $request->validate([
            'contact' => 'required|string',
        ]);

        $user = User::where('email', $data['contact'])
                    ->orWhere('phone', $data['contact'])
                    ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found.',
            ], 404);
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $key = 'pin_reset_' . $user->id;
        Cache::put($key, Hash::make($otp), now()->addMinutes(10));

        Log::info("PIN reset OTP for user {$user->id}: {$otp}");

        return response()->json([
            'success' => true,
            'message' => 'Reset OTP sent.',
            'dev_otp' => config('app.debug') ? $otp : null,
        ]);
    }

    /**
     * Reset PIN after OTP verification.
     */
    public function resetPin(Request $request): JsonResponse
    {
        $data = $request->validate([
            'contact'              => 'required|string',
            'otp'                  => 'required|string|size:6',
            'pin_code'             => 'required|string|min:4|max:6|confirmed',
            'pin_code_confirmation' => 'required|string',
        ]);

        $user = User::where('email', $data['contact'])
                    ->orWhere('phone', $data['contact'])
                    ->firstOrFail();

        $key    = 'pin_reset_' . $user->id;
        $hashed = Cache::get($key);

        if (!$hashed || !Hash::check($data['otp'], $hashed)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
            ], 422);
        }

        Cache::forget($key);
        $user->update(['pin_code' => Hash::make($data['pin_code'])]);

        return response()->json([
            'success' => true,
            'message' => 'PIN reset successfully.',
        ]);
    }
}
