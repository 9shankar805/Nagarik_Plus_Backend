<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DigitalIdController extends Controller
{
    public function generate(Request $request)
    {
        $user = $request->user();
        
        if ($user->kyc_status !== 'verified') {
            return response()->json([
                'success' => false,
                'message' => 'KYC must be verified to generate a Digital ID.',
            ], 403);
        }

        // Create a payload for the QR code.
        // In a real government app, this would be cryptographically signed.
        $payload = json_encode([
            'id' => $user->id,
            'name' => $user->name,
            'citizenship_number' => $user->citizenship_number,
            'kyc_verified_at' => $user->kyc_verified_at,
            'timestamp' => now()->timestamp,
        ]);

        // Generate base64 SVG or PNG
        // We'll return a base64 encoded SVG image string
        $qrCode = QrCode::size(300)->generate($payload);
        
        $base64Qr = base64_encode($qrCode);

        return response()->json([
            'success' => true,
            'message' => 'Digital ID QR generated.',
            'data' => [
                'qr_code_base64' => 'data:image/svg+xml;base64,' . $base64Qr,
            ]
        ]);
    }
}
