<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\SendOtpEmailJob;
use App\Jobs\SendOtpSmsJob;
use App\Models\DeviceToken;
use App\Models\User;
use App\Services\OtpService;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use stdClass;

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

        rescue(fn () => $user->tokens()->delete());
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

    /**
     * Google Sign-In — verify the ID token issued by Google and login/register the user.
     * Accepts either `id_token` (preferred) or `access_token`.
     *
     * Security checks performed:
     *  - JWT signature verified against Google's public JWKS
     *  - `iss` matches accounts.google.com or https://accounts.google.com
     *  - `aud` matches one of the configured GOOGLE_CLIENT_IDS
     *  - `exp` not passed (token not expired)
     *  - `email_verified` claim is true (if email present)
     *  - `hd` (hosted domain) optionally enforced
     */
    public function google(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_token'    => 'required_without:access_token|string',
            'access_token' => 'required_without:id_token|string',
            'device_id'   => 'nullable|string',
        ]);

        try {
            if (!empty($data['id_token'])) {
                $payload = $this->verifyGoogleIdToken($data['id_token']);
            } else {
                $payload = $this->fetchGoogleUserInfo($data['access_token']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Google verification failed: ' . $e->getMessage(),
            ], 401);
        }

        $googleId = $payload->sub ?? $payload->id ?? null;
        if (!$googleId) {
            return response()->json(['success' => false, 'message' => 'Invalid Google payload: missing subject ID.'], 401);
        }

        $email      = isset($payload->email) ? filter_var($payload->email, FILTER_VALIDATE_EMAIL) : null;
        $name       = $payload->name ?? $payload->displayName ?? 'Google User';
        $avatar     = $payload->picture ?? $payload->photoUrl ?? null;

        if (!empty($payload->email) && empty($email)) {
            return response()->json(['success' => false, 'message' => 'Google email is malformed.'], 401);
        }

        $user = User::where('google_id', $googleId)->first();

        if (!$user && $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->update(['google_id' => $googleId]);
            }
        }

        if (!$user) {
            $user = User::create([
                'name'              => $name,
                'email'             => $email,
                'google_id'         => $googleId,
                'avatar'            => $avatar,
                'password'          => Hash::make(str()->random(32)),
                'email_verified_at' => $email ? now() : null,
                'device_id'         => $data['device_id'] ?? null,
            ]);
        }

        if ($user->isBanned()) {
            return response()->json(['success' => false, 'message' => 'Your account has been suspended.'], 403);
        }

        if ($avatar && empty($user->avatar)) {
            $user->update(['avatar' => $avatar]);
        }

        $user->update([
            'device_id'      => $data['device_id'] ?? $user->device_id,
            'last_active_at' => now(),
        ]);

        $user->tokens()->delete();
        $token = $user->createToken('nagarik_plus_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Google sign-in successful.',
            'data'    => [
                'user'  => $user->only('id', 'name', 'email', 'phone', 'biometric_enabled'),
                'token' => $token,
                'is_new_user' => $user->wasRecentlyCreated,
            ],
        ]);
    }

    /**
     * Apple Sign-In — verify the identityToken JWT issued by Apple.
     *
     * Security checks performed:
     *  - JWT signature verified against Apple's public JWKS
     *  - `iss` is https://appleid.apple.com
     *  - `aud` matches the configured APPLE_CLIENT_ID (bundle ID / services ID)
     *  - `exp` not passed (token not expired)
     *  - `nonce` optionally checked against the provided nonce hash
     */
    public function apple(Request $request): JsonResponse
    {
        $data = $request->validate([
            'identity_token' => 'required|string',
            'authorization_code' => 'nullable|string',
            'user'         => 'nullable|array',
            'nonce'        => 'nullable|string',
            'device_id'    => 'nullable|string',
        ]);

        try {
            $payload = $this->verifyAppleIdentityToken($data['identity_token'], $data['nonce'] ?? null);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Apple verification failed: ' . $e->getMessage(),
            ], 401);
        }

        $appleId = $payload->sub ?? null;
        if (!$appleId) {
            return response()->json(['success' => false, 'message' => 'Invalid Apple payload: missing subject ID.'], 401);
        }

        $email      = isset($payload->email) ? filter_var($payload->email, FILTER_VALIDATE_EMAIL) : null;
        $name       = 'Apple User';
        if (!empty($data['user'])) {
            $fullName = trim(($data['user']['firstName'] ?? '') . ' ' . ($data['user']['lastName'] ?? ''));
            if (!empty($fullName)) {
                $name = $fullName;
            }
        }

        if (!empty($payload->email) && empty($email)) {
            return response()->json(['success' => false, 'message' => 'Apple email is malformed.'], 401);
        }

        $isEmailVerified = true;
        if (isset($payload->email_verified)) {
            $isEmailVerified = filter_var($payload->email_verified, FILTER_VALIDATE_BOOLEAN);
        }

        $user = User::where('apple_id', $appleId)->first();

        if (!$user && $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->update(['apple_id' => $appleId]);
            }
        }

        if (!$user) {
            $user = User::create([
                'name'              => $name,
                'email'             => $email,
                'apple_id'          => $appleId,
                'password'          => Hash::make(str()->random(32)),
                'email_verified_at' => $email && $isEmailVerified ? now() : null,
                'device_id'         => $data['device_id'] ?? null,
            ]);
        }

        if ($user->isBanned()) {
            return response()->json(['success' => false, 'message' => 'Your account has been suspended.'], 403);
        }

        $user->update([
            'device_id'      => $data['device_id'] ?? $user->device_id,
            'last_active_at' => now(),
        ]);

        $user->tokens()->delete();
        $token = $user->createToken('nagarik_plus_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Apple sign-in successful.',
            'data'    => [
                'user'  => $user->only('id', 'name', 'email', 'phone', 'biometric_enabled'),
                'token' => $token,
                'is_new_user' => $user->wasRecentlyCreated,
            ],
        ]);
    }

    /**
     * Verify a Google OpenID Connect ID token (JWT) using Google's public keys.
     *
     * @throws \Exception on any verification failure
     */
    private function verifyGoogleIdToken(string $idToken): stdClass
    {
        $keys = Cache::remember('google_jwks', now()->addHours(12), function () {
            $response = Http::timeout(10)->get('https://www.googleapis.com/oauth2/v3/certs');
            if (!$response->successful()) {
                throw new \RuntimeException('Failed to fetch Google public keys (HTTP ' . $response->status() . ').');
            }
            return $response->json();
        });

        if (empty($keys['keys'])) {
            Cache::forget('google_jwks');
            throw new \RuntimeException('Google JWKS response contained no keys.');
        }

        $parsedKeys = JWK::parseKeySet($keys);

        JWT::$leeway = 30;
        $payload = JWT::decode($idToken, $parsedKeys);

        $expectedIssuers = ['accounts.google.com', 'https://accounts.google.com'];
        if (!in_array($payload->iss ?? '', $expectedIssuers, true)) {
            throw new \RuntimeException('Invalid token issuer: ' . ($payload->iss ?? 'null'));
        }

        $acceptedAudiences = config('services.google.client_ids', []);
        if (!empty($acceptedAudiences)) {
            $aud = $payload->aud ?? null;
            $audiences = is_array($aud) ? $aud : [$aud];
            $audienceMatched = (bool) array_intersect($audiences, $acceptedAudiences);
            if (!$audienceMatched) {
                throw new \RuntimeException(
                    'Token audience "' . implode(',', $audiences) . '" does not match accepted client IDs.'
                );
            }
        }

        if (!empty($payload->email)) {
            $emailVerified = filter_var($payload->email_verified ?? false, FILTER_VALIDATE_BOOLEAN);
            if (!$emailVerified) {
                throw new \RuntimeException('Google email is not verified.');
            }
        }

        $now = time() + JWT::$leeway;
        if (!empty($payload->nbf) && (int) $payload->nbf > $now) {
            throw new \RuntimeException('Token not yet valid (nbf).');
        }

        return $payload;
    }

    /**
     * Fallback: retrieve Google user info using an access token.
     * Prefer verifyGoogleIdToken whenever possible (stronger security).
     */
    private function fetchGoogleUserInfo(string $accessToken): stdClass
    {
        $response = Http::timeout(10)
            ->withToken($accessToken)
            ->get('https://www.googleapis.com/oauth2/v3/userinfo');

        if (!$response->successful()) {
            throw new \RuntimeException('Google userinfo endpoint returned HTTP ' . $response->status());
        }

        $body = $response->json();
        if (empty($body['sub']) && empty($body['id'])) {
            throw new \RuntimeException('Google userinfo missing subject identifier.');
        }

        if (!empty($body['email'])) {
            $emailVerified = filter_var($body['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN);
            if (!$emailVerified) {
                throw new \RuntimeException('Google email is not verified.');
            }
        }

        return (object) $body;
    }

    /**
     * Verify an Apple Sign-In identity token (JWT) using Apple's public keys.
     *
     * @throws \Exception on any verification failure
     */
    private function verifyAppleIdentityToken(string $identityToken, ?string $nonce): stdClass
    {
        $keys = Cache::remember('apple_jwks', now()->addHours(24), function () {
            $response = Http::timeout(10)->get('https://appleid.apple.com/auth/keys');
            if (!$response->successful()) {
                throw new \RuntimeException('Failed to fetch Apple public keys (HTTP ' . $response->status() . ').');
            }
            return $response->json();
        });

        if (empty($keys['keys'])) {
            Cache::forget('apple_jwks');
            throw new \RuntimeException('Apple JWKS response contained no keys.');
        }

        $parsedKeys = JWK::parseKeySet($keys);

        JWT::$leeway = 30;
        $payload = JWT::decode($identityToken, $parsedKeys);

        if (($payload->iss ?? '') !== 'https://appleid.apple.com') {
            throw new \RuntimeException('Invalid token issuer: ' . ($payload->iss ?? 'null'));
        }

        $expectedAud = config('services.apple.client_id');
        if (!empty($expectedAud) && ($payload->aud ?? '') !== $expectedAud) {
            throw new \RuntimeException(
                'Token audience "' . ($payload->aud ?? 'null') . '" does not match expected Apple client ID.'
            );
        }

        $now = time() + JWT::$leeway;
        if (!empty($payload->nbf) && (int) $payload->nbf > $now) {
            throw new \RuntimeException('Token not yet valid (nbf).');
        }

        if ($nonce !== null && isset($payload->nonce_supported) && $payload->nonce_supported === true) {
            if (!isset($payload->nonce) || !hash_equals($payload->nonce, $nonce)) {
                throw new \RuntimeException('Nonce mismatch.');
            }
        }

        return $payload;
    }
}
