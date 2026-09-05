<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grievance;
use Illuminate\Http\Request;

class AdminGrievanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Grievance::with(['user', 'category']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $grievances = $query->latest()->paginate(20)->withQueryString();
        return view('admin.grievances.index', compact('grievances'));
    }

    public function show(Grievance $grievance)
    {
        $grievance->load(['user', 'category', 'resolver']);
        return view('admin.grievances.show', compact('grievance'));
    }

    public function update(Request $request, Grievance $grievance)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,rejected',
            'admin_response' => 'nullable|string'
        ]);

        $grievance->update([
            'status' => $request->status,
            'admin_response' => $request->admin_response,
            'resolved_by' => in_array($request->status, ['resolved', 'rejected']) ? $request->user()->id : null,
        ]);

        return back()->with('success', 'Grievance updated successfully.');
    }
}
