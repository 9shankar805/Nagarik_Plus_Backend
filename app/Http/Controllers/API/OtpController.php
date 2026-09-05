<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\SendOtpEmailJob;
use App\Jobs\SendOtpSmsJob;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class OtpController extends Controller
{
    public function __construct(private OtpService $otpService) {}

    /**
     * Send OTP to phone or email (generic — used for identity verification flows).
     */
    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'contact' => 'required|string',
            'type'    => 'required|in:phone,email',
        ]);

        $otp      = $this->otpService->generate($data['contact'], 'generic');
        $isEmail  = $data['type'] === 'email';

        if ($isEmail) {
            dispatch(new SendOtpEmailJob($data['contact'], $otp, 'generic'));
        } else {
            dispatch(new SendOtpSmsJob($data['contact'], $otp));
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully.',
            'dev_otp' => config('app.debug') ? $otp : null,
        ]);
    }

    /**
     * Verify a generic OTP.
     */
    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'contact' => 'required|string',
            'otp'     => 'required|string|size:6',
        ]);

        if (!$this->otpService->verify($data['contact'], $data['otp'], 'generic')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
                'error'   => 'otp_invalid',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP verified.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FORGOT PASSWORD (email) FLOW
    |--------------------------------------------------------------------------
    |  Step 1: POST /auth/forgot-password  { email }
    |          → sends 6-digit OTP to email.
    |  Step 2: POST /auth/reset-password   { email, otp, password, password_confirmation }
    |          → verifies OTP and updates password.
    |
    |  NOTE: We always return success, even if the email doesn't exist in the DB.
    |        This is a standard security measure to prevent account enumeration.
    */

    public function forgotPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $data['email'])->first();

        if ($user) {
            $otp = $this->otpService->generate($data['email'], 'reset_password');
            dispatch(new SendOtpEmailJob($data['email'], $otp, 'reset_password'));
        }

        return response()->json([
            'success' => true,
            'message' => 'If an account with this email exists, a password reset OTP has been sent.',
            'dev_otp' => config('app.debug') && isset($otp) ? $otp : null,
        ]);
    }

    public function verifyResetPasswordOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ]);

        if (!$this->otpService->verify($data['email'], $data['otp'], 'reset_password', false)) { // false = don't delete yet
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
                'error'   => 'otp_invalid',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP verified.',
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'                 => 'required|email',
            'otp'                   => 'required|string|size:6',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['No account found with this email address.'],
            ]);
        }

        if (!$this->otpService->verify($data['email'], $data['otp'], 'reset_password')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
                'error'   => 'otp_invalid',
            ], 422);
        }

        $user->forceFill([
            'password' => Hash::make($data['password']),
        ])->save();

        rescue(fn () => $user->tokens()->delete());

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. You can now log in with your new password.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FORGOT PIN FLOW (existing, improved)
    |--------------------------------------------------------------------------
    |  Step 1: POST /auth/forgot-pin   { contact }  (email OR phone)
    |  Step 2: POST /auth/reset-pin    { contact, otp, pin_code, pin_code_confirmation }
    */

    public function forgotPin(Request $request): JsonResponse
    {
        $data = $request->validate([
            'contact' => 'required|string',
        ]);

        $isEmail = filter_var($data['contact'], FILTER_VALIDATE_EMAIL) !== false;

        $user = $isEmail
            ? User::where('email', $data['contact'])->first()
            : User::where('phone', $data['contact'])->first();

        if (!$user) {
            return response()->json([
                'success' => true,
                'message' => 'If an account with this contact exists, a PIN reset OTP has been sent.',
            ], 200);
        }

        $otp = $this->otpService->generate($data['contact'], 'reset_pin');

        if ($isEmail && !empty($user->email)) {
            dispatch(new SendOtpEmailJob($user->email, $otp, 'reset_pin'));
        } elseif (!empty($user->phone)) {
            dispatch(new SendOtpSmsJob($user->phone, $otp));
        }

        return response()->json([
            'success' => true,
            'message' => 'If an account with this contact exists, a PIN reset OTP has been sent.',
            'dev_otp' => config('app.debug') ? $otp : null,
        ]);
    }

    public function resetPin(Request $request): JsonResponse
    {
        $data = $request->validate([
            'contact'              => 'required|string',
            'otp'                  => 'required|string|size:6',
            'pin_code'             => 'required|string|min:4|max:6|confirmed',
            'pin_code_confirmation' => 'required|string',
        ]);

        $isEmail = filter_var($data['contact'], FILTER_VALIDATE_EMAIL) !== false;

        $user = $isEmail
            ? User::where('email', $data['contact'])->first()
            : User::where('phone', $data['contact'])->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found.',
            ], 404);
        }

        if (!$this->otpService->verify($data['contact'], $data['otp'], 'reset_pin')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
                'error'   => 'otp_invalid',
            ], 422);
        }

        $user->update(['pin_code' => Hash::make($data['pin_code'])]);

        rescue(fn () => $user->tokens()->delete());

        return response()->json([
            'success' => true,
            'message' => 'PIN reset successfully. You can now log in with your new PIN.',
        ]);
    }
}
