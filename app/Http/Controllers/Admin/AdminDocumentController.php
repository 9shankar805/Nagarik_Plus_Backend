<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;

class AdminDocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::with('user');

        if ($request->type)   $query->where('type', $request->type);
        if ($request->status) $query->where('status', $request->status);

        $documents = $query->latest()->paginate(25)->withQueryString();
        return view('admin.documents.index', compact('documents'));
    }

    public function show(Document $document)
    {
        $document->load('user');
        return view('admin.documents.show', compact('document'));
    }
}
