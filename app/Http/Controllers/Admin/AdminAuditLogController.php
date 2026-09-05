<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AdminAuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer');

        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }
        
        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->causer_id);
        }

        $logs = $query->latest()->paginate(50)->withQueryString();

        return view('admin.audit-logs.index', compact('logs'));
    }

    public function show(Activity $activity)
    {
        return view('admin.audit-logs.show', compact('activity'));
    }
}
