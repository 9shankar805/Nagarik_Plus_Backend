<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Verification\VerifyCitizenshipRequest;
use App\Http\Requests\Verification\VerifyLicenceRequest;
use App\Http\Requests\Verification\VerifyNidRequest;
use App\Http\Requests\Verification\VerifyPanRequest;
use App\Services\Verification\CaptchaProxyService;
use App\Services\Verification\Contracts\VerificationResult;
use App\Services\Verification\VerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * VerificationController
 *
 * Exposes government document verification to the Flutter application.
 * The Flutter app NEVER calls government endpoints directly —
 * all traffic flows through this controller.
 *
 * Architecture:
 *
 *   Flutter App
 *       │
 *       ▼
 *   POST /api/v1/verification/{nid|licence|pan|citizenship}
 *       │
 *       ▼
 *   VerificationController   (validates input, maps HTTP response)
 *       │
 *       ▼
 *   VerificationService      (rate limiting, audit logging, timing)
 *       │
 *       ▼
 *   GovernmentVerificationProvider  (adapter — swap per document type)
 *       │
 *       ▼
 *   Government API  (DONIDCR / DoTM / IRD / MoHA)
 *
 * Current status of all providers: PENDING_AUTHORIZATION (RED).
 * The architecture is ready — replace stub adapters with live ones
 * when official API agreements are established.
 */
class VerificationController extends Controller
{
    public function __construct(
        private readonly VerificationService  $service,
        private readonly CaptchaProxyService  $captchaProxy,
    ) {}

    // ─────────────────────────────────────────────────────────────────────
    // POST /api/v1/verification/nid
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @OA\Post(
     *   path="/api/v1/verification/nid",
     *   summary="Verify a Nepal National Identity Number (NIN)",
     *   tags={"Verification"},
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"nin"},
     *       @OA\Property(property="nin", type="string", example="12345678901",
     *                    description="11-digit National Identity Number")
     *     )
     *   ),
     *   @OA\Response(response=200, description="Verification result"),
     *   @OA\Response(response=422, description="Validation error"),
     *   @OA\Response(response=503, description="Provider unavailable / pending authorization")
     * )
     */
    public function verifyNid(VerifyNidRequest $request): JsonResponse
    {
        $result = $this->service->verifyNid(
            ['nin' => $request->validated()['nin']],
            $request,
        );

        return $this->toResponse($result);
    }

    // ─────────────────────────────────────────────────────────────────────
    // POST /api/v1/verification/licence
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @OA\Post(
     *   path="/api/v1/verification/licence",
     *   summary="Verify a Nepal Driving Licence",
     *   tags={"Verification"},
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"licence_number"},
     *       @OA\Property(property="licence_number", type="string",
     *                    example="01-01-12345678",
     *                    description="Format: XX-XX-XXXXXXXX")
     *     )
     *   ),
     *   @OA\Response(response=200, description="Verification result"),
     *   @OA\Response(response=422, description="Validation error"),
     *   @OA\Response(response=503, description="Provider unavailable / pending authorization")
     * )
     */
    public function verifyLicence(VerifyLicenceRequest $request): JsonResponse
    {
        $result = $this->service->verifyLicence(
            ['licence_number' => $request->validated()['licence_number']],
            $request,
        );

        return $this->toResponse($result);
    }

    // ─────────────────────────────────────────────────────────────────────
    // POST /api/v1/verification/pan
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @OA\Post(
     *   path="/api/v1/verification/pan",
     *   summary="Verify a Nepal PAN (Permanent Account Number)",
     *   tags={"Verification"},
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"pan"},
     *       @OA\Property(property="pan", type="string", example="123456789",
     *                    description="9-digit PAN")
     *     )
     *   ),
     *   @OA\Response(response=200, description="Verification result"),
     *   @OA\Response(response=422, description="Validation error"),
     *   @OA\Response(response=503, description="Provider unavailable / pending authorization")
     * )
     */
    public function verifyPan(VerifyPanRequest $request): JsonResponse
    {
        $result = $this->service->verifyPan(
            ['pan' => $request->validated()['pan']],
            $request,
        );

        return $this->toResponse($result);
    }

    // ─────────────────────────────────────────────────────────────────────
    // POST /api/v1/verification/citizenship
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @OA\Post(
     *   path="/api/v1/verification/citizenship",
     *   summary="Verify a Nepal Citizenship Certificate number",
     *   tags={"Verification"},
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"citizenship_number"},
     *       @OA\Property(property="citizenship_number", type="string",
     *                    example="01-075-12345"),
     *       @OA\Property(property="issued_district", type="string",
     *                    example="Kathmandu", nullable=true)
     *     )
     *   ),
     *   @OA\Response(response=200, description="Verification result"),
     *   @OA\Response(response=422, description="Validation error"),
     *   @OA\Response(response=503, description="Provider unavailable / pending authorization")
     * )
     */
    public function verifyCitizenship(VerifyCitizenshipRequest $request): JsonResponse
    {
        $result = $this->service->verifyCitizenship(
            $request->validated(),
            $request,
        );

        return $this->toResponse($result);
    }

    // ─────────────────────────────────────────────────────────────────────
    // GET /api/v1/verification/captcha/nid
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Phase 6 — CAPTCHA-aware user-assisted flow.
     *
     * If DONIDCR requires CAPTCHA (it does), the Flutter app calls this
     * endpoint first to get the image, then displays it to the user.
     * The user types the answer and submits it with their NIN.
     *
     * This endpoint is disabled (503) until DONIDCR grants API access.
     */
    public function nidCaptcha(Request $request): JsonResponse
    {
        $image = $this->captchaProxy->getCaptchaImage();

        if (! $image) {
            return response()->json([
                'success' => false,
                'data' => [
                    'status'  => VerificationResult::STATUS_PENDING_AUTHORIZATION,
                    'message' => 'NID CAPTCHA flow is not yet enabled. '
                               . 'This feature requires an official API agreement with DONIDCR.',
                ],
            ], 503);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'captcha_image_base64' => $image,
                'expires_in_seconds'   => 120,
                'instructions'         => 'Display this image to the user. '
                                        . 'Submit the user\'s answer in the "captcha" field '
                                        . 'when calling POST /verification/nid.',
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // GET /api/v1/verification/status
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Returns the current authorization status of each provider.
     * Lets the Flutter app display appropriate UI (disabled button, info banner).
     */
    public function status(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'nid' => [
                    'available'   => false,
                    'provider'    => 'DONIDCR (Department of National ID and Civil Registration)',
                    'status'      => 'pending_authorization',
                    'description' => 'Requires official API agreement with DONIDCR.',
                ],
                'licence' => [
                    'available'   => false,
                    'provider'    => 'DoTM (Department of Transport Management)',
                    'status'      => 'pending_authorization',
                    'description' => 'DoTM eDL portal offline. Requires official API agreement.',
                ],
                'pan' => [
                    'available'   => false,
                    'provider'    => 'IRD (Inland Revenue Department)',
                    'status'      => 'pending_authorization',
                    'description' => 'No public REST API. Requires official API/MOU with IRD.',
                ],
                'citizenship' => [
                    'available'   => false,
                    'provider'    => 'MoHA (Ministry of Home Affairs)',
                    'status'      => 'pending_authorization',
                    'description' => 'No online citizenship verification service exists. Contact MoHA.',
                ],
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Map a VerificationResult to the correct HTTP status code and envelope.
     */
    private function toResponse(VerificationResult $result): JsonResponse
    {
        $httpStatus = match ($result->status) {
            VerificationResult::STATUS_VERIFIED              => 200,
            VerificationResult::STATUS_NOT_FOUND             => 200,   // not an error, just a result
            VerificationResult::STATUS_MISMATCH              => 200,
            VerificationResult::STATUS_PENDING_AUTHORIZATION => 503,
            VerificationResult::STATUS_CAPTCHA_REQUIRED      => 503,
            VerificationResult::STATUS_PROVIDER_UNAVAILABLE  => 503,
            VerificationResult::STATUS_ERROR                 => 429,   // rate limited
            default                                          => 500,
        };

        return response()->json([
            'success' => $result->verified,
            'data'    => $result->toArray(),
        ], $httpStatus);
    }
}
