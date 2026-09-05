<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VerificationAuditLog;
use Illuminate\Http\Request;

class AdminVerificationController extends Controller
{
    /**
     * Verification audit log viewer.
     * Admins can see attempt counts, statuses, and provider info.
     * Raw document numbers are NEVER displayed (only hashes are stored).
     */
    public function auditLogs(Request $request)
    {
        $query = VerificationAuditLog::with('user:id,name,email')
            ->latest();

        // Filters
        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->paginate(30)->withQueryString();

        // Summary stats
        $stats = [
            'total_today'     => VerificationAuditLog::whereDate('created_at', today())->count(),
            'verified_today'  => VerificationAuditLog::whereDate('created_at', today())->where('verified', true)->count(),
            'pending_auth'    => VerificationAuditLog::where('status', 'pending_authorization')->count(),
            'by_type'         => VerificationAuditLog::selectRaw('document_type, count(*) as total')
                                    ->groupBy('document_type')
                                    ->pluck('total', 'document_type'),
        ];

        return view('admin.verification.audit-logs', compact('logs', 'stats'));
    }
}
