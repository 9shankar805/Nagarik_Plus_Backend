<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\SendOtpEmailJob;
use App\Jobs\SendOtpSmsJob;
use App\Models\DeviceToken;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private OtpService $otpService) {}

    /**
     * Register — sends OTP, does NOT create the account yet.
     * Client must call POST /auth/verify-otp to complete registration.
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'nullable|email|unique:users,email',
            'phone'    => 'nullable|string|unique:users,phone|max:15',
            'password' => 'required|string|min:8|confirmed',
            'device_id'=> 'nullable|string',
        ]);

        if (empty($data['email']) && empty($data['phone'])) {
            return response()->json(['success' => false, 'message' => 'Email or phone is required.'], 422);
        }

        $identifier = $data['email'] ?? $data['phone'];
        $purpose    = 'register';

        // Stash pending registration in cache for 15 min
        $otp = $this->otpService->generate($identifier, $purpose);
        cache()->put("pending_reg_{$identifier}", $data, now()->addMinutes(15));

        if (!empty($data['email'])) {
            dispatch(new SendOtpEmailJob($data['email'], $otp, $data['name']));
        } else {
            dispatch(new SendOtpSmsJob($data['phone'], $otp));
        }

        return response()->json([
            'success'  => true,
            'message'  => 'OTP sent. Please verify to complete registration.',
            'dev_otp'  => config('app.debug') ? $otp : null,
        ], 200);
    }

    /**
     * Verify OTP — completes registration and returns Sanctum token.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'identifier' => 'required|string',
            'otp'        => 'required|string|size:6',
            'purpose'    => 'required|in:register,reset_pin',
        ]);

        $identifier = $data['identifier'];
        $purpose    = $data['purpose'];

        if (!$this->otpService->verify($identifier, $data['otp'], $purpose)) {
            $record = \App\Models\OtpCode::where('identifier', $identifier)
                                          ->where('purpose', $purpose)
                                          ->latest()->first();
            $errorCode = ($record && $record->isExpired()) ? 'otp_expired' : 'otp_invalid';
            return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.', 'error' => $errorCode], 422);
        }

        if ($purpose === 'register') {
            $pending = cache()->get("pending_reg_{$identifier}");
            if (!$pending) {
                return response()->json(['success' => false, 'message' => 'Registration session expired. Please register again.'], 422);
            }

            $user = User::create([
                'name'              => $pending['name'],
                'email'             => $pending['email'] ?? null,
                'phone'             => $pending['phone'] ?? null,
                'password'          => $pending['password'],
                'device_id'         => $pending['device_id'] ?? null,
                'email_verified_at' => now(),
            ]);

            cache()->forget("pending_reg_{$identifier}");
            $token = $user->createToken('nagarik_plus_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Account created successfully.',
                'data'    => ['user' => $user->only('id', 'name', 'email', 'phone'), 'token' => $token],
            ], 201);
        }

        return response()->json(['success' => true, 'message' => 'OTP verified.']);
    }

    /**
     * Login with email + password
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
            'device_id' => 'nullable|string',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Banned user check
        if ($user->isBanned()) {
            return response()->json(['success' => false, 'message' => 'Your account has been suspended. Please contact support.'], 403);
        }

        $user->update([
            'device_id'      => $data['device_id'] ?? $user->device_id,
            'last_active_at' => now(),
        ]);

        $user->tokens()->delete();
        $token = $user->createToken('nagarik_plus_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data'    => [
                'user'  => $user->only('id', 'name', 'email', 'phone', 'biometric_enabled'),
                'token' => $token,
            ],
        ]);
    }

    /**
     * Login with PIN
     */
    public function loginWithPin(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'     => 'required|email',
            'pin_code'  => 'required|string|min:4|max:6',
            'device_id' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['pin_code'], $user->pin_code)) {
            return response()->json(['success' => false, 'message' => 'Invalid PIN.'], 401);
        }

        if ($user->isBanned()) {
            return response()->json(['success' => false, 'message' => 'Your account has been suspended. Please contact support.'], 403);
        }

        if ($user->device_id && $user->device_id !== $data['device_id']) {
            return response()->json([
                'success' => false,
                'message' => 'PIN login is only allowed from your registered device.',
            ], 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('nagarik_plus_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'PIN login successful.',
            'data'    => ['user' => $user->only('id', 'name', 'email', 'phone'), 'token' => $token],
        ]);
    }

    /**
     * Set or update PIN
     */
    public function setPin(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pin'              => 'required_without:pin_code|string|min:4|max:6',
            'pin_code'         => 'required_without:pin|string|min:4|max:6',
            'pin_code_confirm' => 'nullable|string',
            'current_pin'      => 'nullable|string',
            'password'         => 'nullable|string',
        ]);

        $pinValue = $data['pin'] ?? $data['pin_code'];
        $user = $request->user();

        if (!empty($data['password']) && !Hash::check($data['password'], $user->password)) {
            return response()->json(['success' => false, 'message' => 'Password is incorrect.'], 403);
        }

        if (!empty($data['current_pin']) && $user->pin_code && !Hash::check($data['current_pin'], $user->pin_code)) {
            return response()->json(['success' => false, 'message' => 'Current PIN is incorrect.'], 403);
        }

        $user->update(['pin_code' => Hash::make($pinValue)]);

        return response()->json(['success' => true, 'message' => 'PIN updated successfully.']);
    }

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user()->load('documents', 'reminders');

        return response()->json([
            'success' => true,
            'data'    => [
                'user'            => array_merge($user->only('id', 'name', 'email', 'phone', 'dob', 'address', 'citizenship_number', 'avatar', 'biometric_enabled', 'created_at'), [
                    'pin_set' => !empty($user->pin_code),
                    'avatar_url' => $user->avatar ? (str_starts_with($user->avatar, 'http') ? $user->avatar : asset($user->avatar)) : null,
                ]),
                'document_count'  => $user->documents->count(),
                'reminder_count'  => $user->reminders->where('is_enabled', true)->count(),
            ],
        ]);
    }

    /**
     * Update profile.
     * Accepts name, phone, dob, address, citizenship_number, biometric_enabled, avatar file.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'               => 'sometimes|string|max:100',
            'phone'              => 'sometimes|string|max:15|unique:users,phone,' . $request->user()->id,
            'dob'                => 'sometimes|nullable|string',
            'address'            => 'sometimes|nullable|string',
            'citizenship_number' => 'sometimes|nullable|string',
            'biometric_enabled'  => 'sometimes|boolean',
            'avatar'             => 'sometimes|image|max:5120',
        ]);

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = '/storage/' . $path;
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated.',
            'data'    => array_merge($user->only('id', 'name', 'email', 'phone', 'dob', 'address', 'citizenship_number', 'avatar', 'biometric_enabled', 'created_at'), [
                'pin_set' => !empty($user->pin_code),
                'avatar_url' => $user->avatar ? (str_starts_with($user->avatar, 'http') ? $user->avatar : asset($user->avatar)) : null,
            ]),
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Logged out successfully.']);
    }

    /**
     * Register / upsert an FCM device token.
     * POST /api/v1/auth/device-token
     */
    public function registerDeviceToken(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token'    => 'required|string',
            'platform' => 'required|in:android,ios',
        ]);

        DeviceToken::updateOrCreate(
            ['token' => $data['token']],
            [
                'user_id'      => $request->user()->id,
                'platform'     => $data['platform'],
                'last_used_at' => now(),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Device token registered.']);
    }

    /**
     * Update push notification preferences.
     * POST /api/v1/auth/notification-preferences
     */
    public function updateNotificationPreferences(Request $request): JsonResponse
    {
        $data = $request->validate([
            'document_reminders'   => 'sometimes|boolean',
            'news_updates'         => 'sometimes|boolean',
            'system_announcements' => 'sometimes|boolean',
            'learning_updates'     => 'sometimes|boolean',
        ]);

        $user  = $request->user();
        $prefs = array_merge(
            ['document_reminders' => true, 'news_updates' => true, 'system_announcements' => true, 'learning_updates' => true],
            $user->notification_preferences ?? [],
            $data
        );

        $user->update(['notification_preferences' => $prefs]);

        return response()->json(['success' => true, 'message' => 'Preferences updated.', 'data' => $prefs]);
    }
}
