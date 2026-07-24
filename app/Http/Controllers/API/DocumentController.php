<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Services\DocumentService;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends Controller
{
    public function __construct(
        private DocumentService $documentService,
        private SettingService $settingService
    ) {}

    /**
     * List all documents for authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $query = Document::where('user_id', $request->user()->id)
                         ->orderBy('created_at', 'desc');

        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $documents = $query->get()->map(function ($doc) {
            return [
                'id'           => $doc->id,
                'title'        => $doc->title,
                'type'         => $doc->type,
                'status'       => $doc->status,
                'issue_date'   => $doc->issue_date?->toDateString(),
                'expiry_date'  => $doc->expiry_date?->toDateString(),
                'days_left'    => $doc->daysUntilExpiry(),
                'is_expired'   => $doc->isExpired(),
                'is_verified'  => $doc->is_verified,
                'has_file'     => !is_null($doc->file_path),
                'file_name'    => $doc->file_name,
                'created_at'   => $doc->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'documents' => $documents,
                'stats' => [
                    'total'    => $documents->count(),
                    'expiring' => $documents->where('days_left', '<=', 90)->where('days_left', '>=', 0)->count(),
                    'expired'  => $documents->where('is_expired', true)->count(),
                ],
            ],
        ]);
    }

    /**
     * Upload / create a document
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'                => 'required|string|max:100',
            'type'                 => 'required|string|in:national_id,passport,driving_license,pan,citizenship,voter_id,birth_certificate,vehicle_bluebook,insurance,medical,property,academic,other',
            'document_number'      => 'nullable|string|max:50',
            'issue_date'           => 'nullable|date',
            'expiry_date'          => 'nullable|date|after_or_equal:issue_date',
            'issued_by'            => 'nullable|string|max:100',
            'reminder_enabled'     => 'boolean',
            'reminder_days_before' => 'integer|min:1|max:365',
            'metadata'             => 'nullable|array',
            'file'                 => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,heic',
        ]);

        // Task 5.1: Enforce max file size using SettingService
        if ($request->hasFile('file')) {
            $maxMb = (int) $this->settingService->get('max_upload_mb', 10);
            $fileSizeBytes = $request->file('file')->getSize();
            if ($fileSizeBytes > $maxMb * 1024 * 1024) {
                return response()->json([
                    'success' => false,
                    'message' => "File too large. Maximum allowed size is {$maxMb} MB.",
                    'error'   => 'file_too_large',
                ], 422);
            }
        }

        $document = $this->documentService->store(
            $request->user(),
            $data,
            $request->file('file')
        );

        return response()->json([
            'success' => true,
            'message' => 'Document saved securely.',
            'data'    => ['document_id' => $document->id, 'title' => $document->title],
        ], 201);
    }

    /**
     * Get a single document (decrypted) — owner only
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $document = Document::where('id', $id)
                            ->where('user_id', $request->user()->id)
                            ->firstOrFail();

        $decrypted = $this->documentService->getDecrypted($document);

        return response()->json([
            'success' => true,
            'data'    => $decrypted,
        ]);
    }

    /**
     * Update document metadata
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $document = Document::where('id', $id)
                            ->where('user_id', $request->user()->id)
                            ->firstOrFail();

        $data = $request->validate([
            'title'                => 'sometimes|string|max:100',
            'expiry_date'          => 'sometimes|nullable|date',
            'reminder_enabled'     => 'sometimes|boolean',
            'reminder_days_before' => 'sometimes|integer|min:1|max:365',
        ]);

        $document->update($data);

        return response()->json(['success' => true, 'message' => 'Document updated.']);
    }

    /**
     * Delete a document (moves file to quarantine, soft-deletes DB record)
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $document = Document::where('id', $id)
                            ->where('user_id', $request->user()->id)
                            ->firstOrFail();

        // Task 5.2: Move file to quarantine instead of hard-deleting
        if ($document->file_path && Storage::exists($document->file_path)) {
            $quarantinePath = "quarantine/{$document->user_id}/" . basename($document->file_path);
            Storage::move($document->file_path, $quarantinePath);
        }

        // Soft-delete the DB record (Document model uses SoftDeletes)
        $document->delete();

        return response()->json(['success' => true, 'message' => 'Document deleted.']);
    }

    /**
     * Stream download of the encrypted document file
     *
     * Task 5.3: Uses Storage::download() which returns a Symfony StreamedResponse
     * with Content-Disposition: attachment set automatically.
     * Returns 403 if the authenticated user does not own the document.
     * Returns 404 if no file is attached or the file does not exist in storage.
     */
    public function download(Request $request, int $id)
    {
        // Fetch document without scoping to user so we can return a proper 403
        $document = Document::findOrFail($id);

        // Task 5.3: Explicit ownership check — return 403 if not owner
        if ($document->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        // Task 5.3: Return 404 if no file attached
        if (!$document->file_path) {
            return response()->json(['success' => false, 'message' => 'No file attached.'], 404);
        }

        // Task 5.3: Return 404 if file is missing from storage
        if (!Storage::exists($document->file_path)) {
            return response()->json(['success' => false, 'message' => 'File not found.'], 404);
        }

        $fileName = $document->file_name ?? 'document';

        // Storage::download() returns a Symfony StreamedResponse with
        // Content-Disposition: attachment; filename="..."
        return Storage::download($document->file_path, $fileName, [
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Documents expiring soon
     */
    public function expiringSoon(Request $request): JsonResponse
    {
        $days = $request->get('days', 90);

        $documents = Document::where('user_id', $request->user()->id)
                             ->expiringSoon($days)
                             ->get()
                             ->map(fn($doc) => [
                                 'id'          => $doc->id,
                                 'title'       => $doc->title,
                                 'type'        => $doc->type,
                                 'expiry_date' => $doc->expiry_date?->toDateString(),
                                 'days_left'   => $doc->daysUntilExpiry(),
                             ]);

        return response()->json(['success' => true, 'data' => $documents]);
    }
}
