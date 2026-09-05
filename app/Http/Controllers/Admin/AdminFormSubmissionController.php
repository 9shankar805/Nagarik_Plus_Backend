<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use Illuminate\Http\Request;

class AdminFormSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = FormSubmission::with(['user', 'vitalEvent', 'documentTemplate'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $submissions = $query->paginate(15);
        return view('admin.form-submissions.index', compact('submissions'));
    }

    public function show(FormSubmission $formSubmission)
    {
        $formSubmission->load(['user', 'vitalEvent', 'documentTemplate', 'reviewer']);
        return view('admin.form-submissions.show', compact('formSubmission'));
    }

    public function update(Request $request, FormSubmission $formSubmission)
    {
        $request->validate([
            'status'           => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string',
        ]);

        $formSubmission->update([
            'status'           => $request->status,
            'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
            'reviewed_by'      => $request->user()->id,
        ]);

        return redirect()->route('admin.form-submissions.index')->with('success', 'Form submission updated successfully.');
    }

    public function destroy(FormSubmission $formSubmission)
    {
        $formSubmission->delete();
        return redirect()->route('admin.form-submissions.index')->with('success', 'Form submission deleted.');
    }
}
