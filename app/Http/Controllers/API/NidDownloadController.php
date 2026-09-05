<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

/**
 * DONIDCR eNID Download — complete 3-step flow
 *
 * Step 1  GET  /api/v1/nid/captcha          → proxies captcha image from DONIDCR
 * Step 2  POST /api/v1/nid/request-otp      → sends user details + captcha → DONIDCR sends OTP to citizen phone
 * Step 3  POST /api/v1/nid/download         → verifies OTP → fetches NIN info + streams PDF
 *
 * All calls are server-side (no CORS), so the browser never touches donidcr.gov.np directly.
 *
 * Exact DONIDCR API (reverse-engineered from citizenportal.donidcr.gov.np JS bundle):
 *   CAPTCHA:      GET  https://captcha-citizenportal.donidcr.gov.np/api/captcha
 *   Request OTP:  POST https://api-citizenportal.donidcr.gov.np/api/v1/mfa/request-otp
 *                      Header: Captcha-Code: <value_user_typed>
 *   Verify OTP:   POST https://api-citizenportal.donidcr.gov.np/api/v1/mfa/verify-otp
 *                      → returns { code:"OTP_VERIFIED", downloadToken:"..." }
 *   Get NIN info: POST https://api-citizenportal.donidcr.gov.np/api/v1/ccn
 *                      Header: X-Download-Token: <downloadToken>
 *   Download PDF: POST https://api-citizenportal.donidcr.gov.np/api/v1/enid/download
 *                      Header: X-Download-Token: <downloadToken>  Accept: application/pdf
 *                      → returns PDF blob
 */
class NidDownloadController extends Controller
{
    private const CAPTCHA_URL   = 'https://captcha-citizenportal.donidcr.gov.np/api/captcha';
    private const API_BASE      = 'https://api-citizenportal.donidcr.gov.np';
    private const ORIGIN        = 'https://citizenportal.donidcr.gov.np';
    private const REFERER       = 'https://citizenportal.donidcr.gov.np/en/check-national-id-number';
    private const TIMEOUT       = 20;

    // ── Step 1: GET /api/v1/nid/captcha ──────────────────────────────────

    public function captcha(Request $request): JsonResponse
    {
        // Rate-limit: 20 captcha fetches per minute per IP
        $key = 'nid_captcha:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 20)) {
            return response()->json(['success' => false, 'message' => 'Too many requests.'], 429);
        }
        RateLimiter::hit($key, 60);

        try {
            $resp = Http::timeout(self::TIMEOUT)
                ->withoutVerifying()   // DONIDCR uses self-signed/intermediate cert — safe for gov server
                ->withHeaders([
                    'Origin'  => self::ORIGIN,
                    'Referer' => self::REFERER,
                    'Accept'  => 'application/json',
                ])
                ->get(self::CAPTCHA_URL);

            if (!$resp->successful()) {
                return response()->json(['success' => false, 'message' => 'DONIDCR captcha service unavailable.'], 503);
            }

            return response()->json([
                'success' => true,
                'data'    => [
                    'image' => $resp->json('image'),   // base64 PNG
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('NID captcha fetch failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to load captcha. Try again.'], 503);
        }
    }

    // ── Step 2: POST /api/v1/nid/request-otp ─────────────────────────────

    public function requestOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'full_name'             => 'required|string|max:200',
            'full_name_np'          => 'required|string|max:200',
            'dob_bs'                => 'required|string|max:20',
            'citizenship_issued_bs' => 'required|string|max:20',
            'captcha'               => 'required|string|size:6',
        ]);

        // Rate-limit: 5 OTP requests per 10 min per user/IP
        $key = 'nid_otp:' . ($request->user()?->id ?? $request->ip());
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json(['success' => false, 'message' => 'Too many OTP requests. Wait 10 minutes.'], 429);
        }
        RateLimiter::hit($key, 600);

        try {
            $resp = Http::timeout(self::TIMEOUT)
                ->withoutVerifying()
                ->withHeaders([
                    'Origin'       => self::ORIGIN,
                    'Referer'      => self::REFERER,
                    'Accept'       => 'application/json',
                    'Captcha-Code' => $data['captcha'],       // ← DONIDCR requires this header
                ])
                ->post(self::API_BASE . '/api/v1/mfa/request-otp', [
                    'fullName'          => strtoupper(trim($data['full_name'])),
                    'fullNameLoc'       => trim($data['full_name_np']),
                    'dobLoc'            => trim($data['dob_bs']),
                    'ccnIssuingDateLoc' => trim($data['citizenship_issued_bs']),
                ]);

            $body = $resp->json();
            $code = $body['code'] ?? null;

            if ($resp->status() === 422 || $code === 'INVALID_CAPTCHA') {
                return response()->json(['success' => false, 'message' => 'Invalid CAPTCHA. Please refresh and try again.', 'error' => 'invalid_captcha'], 422);
            }

            if (!$resp->successful()) {
                Log::warning('DONIDCR request-otp failed', ['status' => $resp->status(), 'body' => $body]);
                return response()->json(['success' => false, 'message' => $body['message'] ?? 'OTP request failed.'], 422);
            }

            // body contains transactionId — pass it back to the frontend
            return response()->json([
                'success' => true,
                'message' => 'OTP sent to your registered mobile number.',
                'data'    => [
                    'transaction_id' => $body['transactionId'] ?? $body['transaction_id'] ?? null,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('NID request-otp exception', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Service error. Please try again.'], 503);
        }
    }

    // ── Step 3: POST /api/v1/nid/download ────────────────────────────────

    public function download(Request $request)
    {
        $data = $request->validate([
            'transaction_id'        => 'required|string',
            'otp'                   => 'required|string|size:6',
            'full_name'             => 'required|string|max:200',
            'full_name_np'          => 'required|string|max:200',
            'dob_bs'                => 'required|string|max:20',
            'citizenship_issued_bs' => 'required|string|max:20',
        ]);

        // Rate-limit: 3 download attempts per 10 min
        $key = 'nid_dl:' . ($request->user()?->id ?? $request->ip());
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json(['success' => false, 'message' => 'Too many attempts. Wait 10 minutes.'], 429);
        }
        RateLimiter::hit($key, 600);

        try {
            // ── 3a: Verify OTP ───────────────────────────────────────────
            $verifyResp = Http::timeout(self::TIMEOUT)
                ->withoutVerifying()
                ->withHeaders([
                    'Origin'  => self::ORIGIN,
                    'Referer' => self::REFERER,
                    'Accept'  => 'application/json',
                ])
                ->post(self::API_BASE . '/api/v1/mfa/verify-otp', [
                    'transactionId' => $data['transaction_id'],
                    'otp'           => trim($data['otp']),
                ]);

            $verifyBody = $verifyResp->json();
            $verifyCode = $verifyBody['code'] ?? null;

            if ($verifyCode === 'OTP_INVALID') {
                $remaining = $verifyBody['attemptsRemaining'] ?? '?';
                return response()->json(['success' => false, 'message' => "Invalid OTP. {$remaining} attempt(s) remaining.", 'error' => 'otp_invalid'], 422);
            }
            if ($verifyCode === 'OTP_EXPIRED') {
                return response()->json(['success' => false, 'message' => 'OTP has expired. Please start again.', 'error' => 'otp_expired'], 422);
            }
            if ($verifyCode === 'OTP_LOCKED') {
                $mins = isset($verifyBody['retryAfterSeconds']) ? ceil($verifyBody['retryAfterSeconds'] / 60) : 30;
                return response()->json(['success' => false, 'message' => "Too many wrong OTPs. Try again in {$mins} minute(s).", 'error' => 'otp_locked'], 422);
            }
            if ($verifyCode !== 'OTP_VERIFIED' || empty($verifyBody['downloadToken'])) {
                return response()->json(['success' => false, 'message' => $verifyBody['message'] ?? 'OTP verification failed.'], 422);
            }

            $downloadToken = $verifyBody['downloadToken'];
            $commonHeaders = [
                'Origin'           => self::ORIGIN,
                'Referer'          => self::REFERER,
                'X-Download-Token' => $downloadToken,
            ];
            $userPayload = [
                'full_name'             => strtoupper(trim($data['full_name'])),
                'full_name_loc'         => trim($data['full_name_np']),
                'dob_loc'               => trim($data['dob_bs']),
                'ccn_issuing_date_loc'  => trim($data['citizenship_issued_bs']),
            ];

            // ── 3b: Fetch NIN info (ccn) ─────────────────────────────────
            $ninResp = Http::timeout(self::TIMEOUT)
                ->withoutVerifying()
                ->withHeaders(array_merge($commonHeaders, ['Accept' => 'application/json']))
                ->post(self::API_BASE . '/api/v1/ccn', $userPayload);

            $ninData = $ninResp->json();

            // ── 3c: Download PDF ─────────────────────────────────────────
            $pdfResp = Http::timeout(self::TIMEOUT)
                ->withoutVerifying()
                ->withHeaders(array_merge($commonHeaders, [
                    'Accept'       => 'application/pdf',
                    'Content-Type' => 'application/json',
                ]))
                ->post(self::API_BASE . '/api/v1/enid/download', $userPayload);

            if (!$pdfResp->successful() || $pdfResp->status() === 401) {
                Log::warning('DONIDCR enid/download failed', ['status' => $pdfResp->status()]);
                return response()->json(['success' => false, 'message' => 'Failed to download eNID. Please try again.'], 422);
            }

            $nin     = $ninData['nin']     ?? $ninData['nin_loc']     ?? null;
            $ninLoc  = $ninData['nin_loc'] ?? null;
            $filename = 'eNID_' . now()->format('YmdHis') . '.pdf';

            // Stream PDF directly to browser
            return response($pdfResp->body(), 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'X-NIN'               => $nin ?? '',    // expose NIN in header for Flutter
                'X-NIN-Loc'           => $ninLoc ?? '',
            ]);
        } catch (\Throwable $e) {
            Log::error('NID download exception', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Service error. Please try again.'], 503);
        }
    }
}
