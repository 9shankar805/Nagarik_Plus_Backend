<?php

namespace App\Livewire\User;

use App\Models\ActivityLog;
use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class DocumentTable extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $activeTab = 'documents'; // 'documents' or 'history'
    public $search = '';
    public $typeFilter = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $confirmDeleteId = null;

    // Add/Edit form fields
    public $title = '';
    public $type = '';
    public $document_number = '';
    public $issue_date = '';
    public $expiry_date = '';
    public $file = null;
    public $editId = null;

    // Document Preview
    public $previewDocument = null;
    public $previewDataUri = null;
    public $previewDecryptedData = [];

    protected function rules()
    {
        return [
            'title' => 'required|string|max:100',
            'type' => 'required|string|in:national_id,passport,driving_license,pan,citizenship,voter_id,birth_certificate,vehicle_bluebook,insurance,medical,property,academic,other',
            'document_number' => 'nullable|string|max:50',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'file' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,heic',
        ];
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $query = Document::where('user_id', auth()->id());

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        $documents = $query->orderBy($this->sortBy, $this->sortDirection)->paginate(10);

        $activityLogs = ActivityLog::where('user_id', auth()->id())
            ->whereIn('action', ['document_added', 'document_updated', 'document_deleted', 'document_viewed', 'document_downloaded'])
            ->latest()
            ->paginate(10, ['*'], 'historyPage');

        $userId = auth()->id();
        $stats = [
            'total' => Document::where('user_id', $userId)->count(),
            'expiring_soon' => Document::where('user_id', $userId)->expiringSoon(30)->count(),
            'expired' => Document::where('user_id', $userId)->whereNotNull('expiry_date')->where('expiry_date', '<', now())->count(),
        ];

        $typeImages = [
            'national_id' => 'nid1752476653129.png',
            'passport' => 'passport1752476337775.png',
            'driving_license' => 'license1752476621950.png',
            'pan' => 'pan.png',
            'citizenship' => 'cit1759940267390.png',
            'voter_id' => 'voterid.png',
            'birth_certificate' => 'birthcertificate.png',
            'vehicle_bluebook' => 'SSF1752476396810.png',
            'insurance' => 'cims1752476325868.png',
            'medical' => 'pcr1752476863055.png',
            'property' => 'dolma1752476593369.png',
            'academic' => 'slc1631011325238.jpg',
            'other' => 'unnamed.webp',
        ];

        return view('livewire.user.document-table', [
            'documents' => $documents,
            'activityLogs' => $activityLogs,
            'types' => Document::TYPES,
            'stats' => $stats,
            'typeImages' => $typeImages,
        ]);
    }

    public function resetForm()
    {
        $this->title = '';
        $this->type = '';
        $this->document_number = '';
        $this->issue_date = '';
        $this->expiry_date = '';
        $this->file = null;
        $this->editId = null;
        $this->resetValidation();
    }

    public function save(DocumentService $documentService)
    {
        $this->validate();

        if ($this->editId) {
            $document = Document::where('id', $this->editId)->where('user_id', auth()->id())->firstOrFail();
            $document->update([
                'title' => $this->title,
                'type' => $this->type,
                'document_number' => $this->document_number,
                'issue_date' => $this->issue_date,
                'expiry_date' => $this->expiry_date,
            ]);

            if ($this->file) {
                // Delete old file
                if ($document->file_path && Storage::exists($document->file_path)) {
                    Storage::delete($document->file_path);
                }
                // Upload new file
                $documentService->store(auth()->user(), $this->all(), $this->file, $document);
            }

            ActivityLog::record('document_updated', auth()->user(), "Updated document: {$document->title}", [
                'subject_type' => Document::class,
                'subject_id' => $document->id,
            ]);

            session()->flash('success', 'Document updated successfully.');
        } else {
            $document = $documentService->store(auth()->user(), $this->all(), $this->file);
            
            ActivityLog::record('document_added', auth()->user(), "Uploaded document: {$document->title}", [
                'subject_type' => Document::class,
                'subject_id' => $document->id,
            ]);

            session()->flash('success', 'Document saved securely.');
        }

        $this->resetForm();
    }

    public function edit($id)
    {
        $document = Document::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $this->editId = $document->id;
        $this->title = $document->title;
        $this->type = $document->type;
        $this->document_number = $document->document_number;
        $this->issue_date = $document->issue_date?->toDateString();
        $this->expiry_date = $document->expiry_date?->toDateString();
    }

    public function preview($id, DocumentService $documentService)
    {
        $document = Document::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $this->previewDocument = $document;
        $this->previewDecryptedData = $documentService->getDecrypted($document);
        $this->previewDataUri = $documentService->getDecryptedFileDataUri($document);

        ActivityLog::record('document_viewed', auth()->user(), "Viewed preview of document: {$document->title}", [
            'subject_type' => Document::class,
            'subject_id' => $document->id,
        ]);
    }

    public function closePreview()
    {
        $this->previewDocument = null;
        $this->previewDataUri = null;
        $this->previewDecryptedData = [];
    }

    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }

    public function delete(DocumentService $documentService)
    {
        $document = Document::where('id', $this->confirmDeleteId)->where('user_id', auth()->id())->firstOrFail();
        $title = $document->title;
        $documentService->delete($document);
        
        ActivityLog::record('document_deleted', auth()->user(), "Deleted document: {$title}", [
            'subject_type' => Document::class,
            'subject_id' => $document->id,
        ]);

        $this->confirmDeleteId = null;
        session()->flash('success', 'Document deleted.');
    }

    public function download($id)
    {
        $document = Document::findOrFail($id);
        if ($document->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$document->file_path || !Storage::exists($document->file_path)) {
            session()->flash('error', 'File not found.');
            return;
        }

        ActivityLog::record('document_downloaded', auth()->user(), "Downloaded document: {$document->title}", [
            'subject_type' => Document::class,
            'subject_id' => $document->id,
        ]);

        return Storage::download($document->file_path, $document->file_name ?? 'document');
    }

    public function sort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }
}

