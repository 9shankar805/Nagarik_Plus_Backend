<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use App\Models\VitalEvent;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormSubmissionController extends Controller
{
    /**
     * Submit a dynamic digital form
     */
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vital_event_id'       => 'nullable|exists:vital_events,id',
            'document_template_id' => 'nullable|exists:document_templates,id',
            'form_data'            => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        if (!$request->vital_event_id && !$request->document_template_id) {
            return response()->json([
                'success' => false,
                'message' => 'Must provide either vital_event_id or document_template_id'
            ], 422);
        }

        $submission = FormSubmission::create([
            'user_id'              => $request->user()->id,
            'vital_event_id'       => $request->vital_event_id,
            'document_template_id' => $request->document_template_id,
            'form_data'            => $request->form_data,
            'status'               => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Form submitted successfully',
            'data'    => $submission
        ], 201);
    }

    /**
     * List user's submissions
     */
    public function index(Request $request)
    {
        $submissions = FormSubmission::where('user_id', $request->user()->id)
            ->with(['vitalEvent', 'documentTemplate'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $submissions
        ]);
    }
}
