<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $reminders = Reminder::where('user_id', $request->user()->id)
                             ->with('document:id,title,type')
                             ->orderBy('due_date')
                             ->get()
                             ->map(fn($r) => [
                                 'id'              => $r->id,
                                 'title'           => $r->title,
                                 'description'     => $r->description,
                                 'due_date'        => $r->due_date->toDateString(),
                                 'days_remaining'  => $r->daysRemaining(),
                                 'urgency'         => $r->urgencyLevel(),
                                 'is_enabled'      => $r->is_enabled,
                                 'document'        => $r->document,
                             ]);

        return response()->json(['success' => true, 'data' => $reminders]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'       => 'required|string|max:100',
            'description' => 'nullable|string',
            'due_date'    => 'required|date|after:today',
            'days_before' => 'sometimes|integer|min:1|max:365',
            'document_id' => 'nullable|integer|exists:documents,id',
        ]);

        // Ensure document belongs to user
        if (!empty($data['document_id'])) {
            $doc = \App\Models\Document::where('id', $data['document_id'])
                                       ->where('user_id', $request->user()->id)
                                       ->first();
            if (!$doc) {
                return response()->json(['success' => false, 'message' => 'Document not found.'], 404);
            }
        }

        $reminder = Reminder::create([
            'user_id'     => $request->user()->id,
            'document_id' => $data['document_id'] ?? null,
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'due_date'    => $data['due_date'],
            'days_before' => $data['days_before'] ?? 30,
            'is_enabled'  => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reminder created.',
            'data'    => ['id' => $reminder->id],
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $reminder = Reminder::where('id', $id)
                            ->where('user_id', $request->user()->id)
                            ->firstOrFail();

        $data = $request->validate([
            'title'       => 'sometimes|string|max:100',
            'due_date'    => 'sometimes|date',
            'is_enabled'  => 'sometimes|boolean',
            'days_before' => 'sometimes|integer|min:1|max:365',
        ]);

        $reminder->update($data);

        return response()->json(['success' => true, 'message' => 'Reminder updated.']);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        Reminder::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail()
                ->delete();

        return response()->json(['success' => true, 'message' => 'Reminder deleted.']);
    }
}
