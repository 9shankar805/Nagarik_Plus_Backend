<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    public function __construct(private EncryptionService $encryption) {}

    /**
     * Store a new document with optional file upload
     */
    public function store(User $user, array $data, ?UploadedFile $file = null): Document
    {
        // Encrypt sensitive fields — merge holder_name into metadata
        $metaData = $data['metadata'] ?? [];
        if (!empty($data['holder_name'])) {
            $metaData['holder_name'] = $data['holder_name'];
        }
        $sensitiveFields = array_filter([
            'document_number' => $data['document_number'] ?? null,
            'issued_by'       => $data['issued_by'] ?? null,
            'metadata'        => $metaData ?: null,
        ]);
        $encryptedData = $this->encryption->encrypt($sensitiveFields);

        $filePath = null;
        $fileName = null;
        $mimeType = null;
        $fileSize = null;

        if ($file) {
            // Store encrypted file
            $encryptedContent = $this->encryption->encryptFile($file->getPathname());
            $fileName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();

            $path = "documents/{$user->id}/" . Str::uuid() . '.enc';
            Storage::put($path, $encryptedContent);
            $filePath = $path;
        }

        $document = Document::create([
            'user_id'              => $user->id,
            'title'                => $data['title'],
            'type'                 => $data['type'],
            'document_number'      => null, // stored encrypted
            'encrypted_data'       => $encryptedData,
            'file_path'            => $filePath,
            'file_name'            => $fileName,
            'mime_type'            => $mimeType,
            'file_size'            => $fileSize,
            'issue_date'           => !empty($data['issue_date']) ? $data['issue_date'] : null,
            'expiry_date'          => !empty($data['expiry_date']) ? $data['expiry_date'] : null,
            'issued_by'            => null, // stored encrypted
            'status'               => 'active',
            'reminder_enabled'     => $data['reminder_enabled'] ?? true,
            'reminder_days_before' => $data['reminder_days_before'] ?? 30,
            // Store metadata (including holder_name) unencrypted for fast listing
            'metadata'             => $metaData ?: null,
        ]);

        // Auto-create reminder if expiry date provided
        if (!empty($data['expiry_date']) && ($data['reminder_enabled'] ?? true)) {
            Reminder::create([
                'user_id'     => $user->id,
                'document_id' => $document->id,
                'title'       => "Renew {$document->title}",
                'due_date'    => $data['expiry_date'],
                'days_before' => $data['reminder_days_before'] ?? 30,
                'is_enabled'  => true,
            ]);
        }

        return $document;
    }

    /**
     * Get decrypted document data for the owner
     */
    public function getDecrypted(Document $document): array
    {
        $doc = $document->toArray();
        if ($document->encrypted_data) {
            $decrypted = $this->encryption->decrypt($document->encrypted_data);
            if (is_array($decrypted)) {
                $doc = array_merge($doc, $decrypted);
            }
        }
        unset($doc['encrypted_data'], $doc['file_path']);
        return $doc;
    }

    /**
     * Get a signed download URL for an encrypted document file
     */
    public function getDownloadUrl(Document $document): ?string
    {
        if (!$document->file_path) return null;
        return Storage::temporaryUrl($document->file_path, now()->addMinutes(5));
    }

    /**
     * Decrypt document file content and return as base64 Data URI for inline browser preview
     */
    public function getDecryptedFileDataUri(Document $document): ?string
    {
        if (!$document->file_path || !Storage::exists($document->file_path)) {
            return null;
        }

        $encryptedContent = Storage::get($document->file_path);
        $decryptedBinary = $this->encryption->decrypt($encryptedContent);

        if (!$decryptedBinary || !is_string($decryptedBinary)) {
            return null;
        }

        $mimeType = $document->mime_type ?? 'application/octet-stream';
        $base64 = base64_encode($decryptedBinary);

        return "data:{$mimeType};base64,{$base64}";
    }

    /**
     * Delete document — moves file to quarantine, then soft-deletes the DB record.
     */
    public function delete(Document $document): void
    {
        if ($document->file_path && Storage::exists($document->file_path)) {
            $quarantinePath = "quarantine/{$document->user_id}/" . basename($document->file_path);
            Storage::move($document->file_path, $quarantinePath);
        }
        $document->reminder()->delete();
        $document->delete(); // soft-delete (model uses SoftDeletes)
    }
}
