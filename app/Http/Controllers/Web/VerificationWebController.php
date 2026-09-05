<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\VerificationAuditLog;
use Illuminate\Http\Request;

class VerificationWebController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // User's own verification history (no raw document numbers — only status)
        $history = VerificationAuditLog::where('user_id', $user->id)
            ->latest()
            ->take(20)
            ->get(['document_type', 'status', 'verified', 'created_at']);

        // Provider status data (mirrors the API status endpoint)
        $providers = [
            [
                'label'          => 'NID',
                'provider_short' => 'DONIDCR',
                'available'      => false,
                'status'         => 'pending_authorization',
            ],
            [
                'label'          => 'Licence',
                'provider_short' => 'DoTM',
                'available'      => false,
                'status'         => 'pending_authorization',
            ],
            [
                'label'          => 'PAN',
                'provider_short' => 'IRD',
                'available'      => false,
                'status'         => 'pending_authorization',
            ],
            [
                'label'          => 'Citizenship',
                'provider_short' => 'MoHA',
                'available'      => false,
                'status'         => 'pending_authorization',
            ],
        ];

        return view('user.verification', compact('history', 'providers'));
    }
}
