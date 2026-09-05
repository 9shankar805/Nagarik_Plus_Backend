<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminDocumentTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentTemplate::query();

        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(fn ($q) => $q->where('title', 'like', $search)->orWhere('description', 'like', $search));
        }

        $templates = $query->ordered()->latest('id')->paginate(20)->withQueryString();
        $categories = DocumentTemplate::CATEGORIES;

        return view('admin.document-templates.index', compact('templates', 'categories'));
    }

    public function create()
    {
        $categories = DocumentTemplate::CATEGORIES;
        return view('admin.document-templates.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'slug'                => 'nullable|string|max:255|unique:document_templates,slug',
            'category'            => 'nullable|string|max:100',
            'description'         => 'nullable|string',
            'preview_image'       => 'nullable|string|max:500',
            'template_file'       => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,svg,ai,psd,zip|max:20480',
            'sort_order'          => 'nullable|integer|min:0',
            'is_active'           => 'nullable|boolean',
            'is_featured'         => 'nullable|boolean',
            'guidelines'          => 'nullable|string',
            'field_labels'        => 'nullable|array',
            'field_names'         => 'nullable|array',
            'field_types'         => 'nullable|array',
            'field_required'      => 'nullable|array',
        ]);

        $slug = $validated['slug'] ?: Str::slug($validated['title']);
        $slug = $this->makeUniqueSlug($slug);

        $template = new DocumentTemplate($validated);
        $template->slug = $slug;
        $template->sort_order = $validated['sort_order'] ?? 0;
        $template->is_active  = $request->boolean('is_active', true);
        $template->is_featured = $request->boolean('is_featured', false);

        if ($request->hasFile('template_file')) {
            $file = $request->file('template_file');
            $path = $file->store('document-templates', 'public');
            $template->template_file_path = $path;
            $template->template_file_name = $file->getClientOriginalName();
            $template->template_file_size = $file->getSize();
        }

        $template->placeholder_fields = $this->buildPlaceholderFields($validated);
        $template->save();

        return redirect()->route('admin.document-templates.index')->with('success', 'Document template created successfully.');
    }

    public function show(DocumentTemplate $documentTemplate)
    {
        return view('admin.document-templates.show', ['template' => $documentTemplate]);
    }

    public function edit(DocumentTemplate $documentTemplate)
    {
        $categories = DocumentTemplate::CATEGORIES;
        return view('admin.document-templates.edit', [
            'template'   => $documentTemplate,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, DocumentTemplate $documentTemplate)
    {
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'slug'                => 'nullable|string|max:255|unique:document_templates,slug,' . $documentTemplate->id,
            'category'            => 'nullable|string|max:100',
            'description'         => 'nullable|string',
            'preview_image'       => 'nullable|string|max:500',
            'template_file'       => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,svg,ai,psd,zip|max:20480',
            'remove_file'         => 'nullable|boolean',
            'sort_order'          => 'nullable|integer|min:0',
            'is_active'           => 'nullable|boolean',
            'is_featured'         => 'nullable|boolean',
            'guidelines'          => 'nullable|string',
            'field_labels'        => 'nullable|array',
            'field_names'         => 'nullable|array',
            'field_types'         => 'nullable|array',
            'field_required'      => 'nullable|array',
        ]);

        $slug = $validated['slug'] ?: Str::slug($validated['title']);
        if ($slug !== $documentTemplate->slug) {
            $slug = $this->makeUniqueSlug($slug, $documentTemplate->id);
        }

        $documentTemplate->fill($validated);
        $documentTemplate->slug = $slug;
        $documentTemplate->sort_order = $validated['sort_order'] ?? 0;
        $documentTemplate->is_active   = $request->boolean('is_active', true);
        $documentTemplate->is_featured = $request->boolean('is_featured', false);

        if ($request->hasFile('template_file')) {
            if ($documentTemplate->template_file_path && Storage::disk('public')->exists($documentTemplate->template_file_path)) {
                Storage::disk('public')->delete($documentTemplate->template_file_path);
            }
            $file = $request->file('template_file');
            $path = $file->store('document-templates', 'public');
            $documentTemplate->template_file_path = $path;
            $documentTemplate->template_file_name = $file->getClientOriginalName();
            $documentTemplate->template_file_size = $file->getSize();
        } elseif ($request->boolean('remove_file') && $documentTemplate->template_file_path) {
            if (Storage::disk('public')->exists($documentTemplate->template_file_path)) {
                Storage::disk('public')->delete($documentTemplate->template_file_path);
            }
            $documentTemplate->template_file_path = null;
            $documentTemplate->template_file_name = null;
            $documentTemplate->template_file_size = null;
        }

        $documentTemplate->placeholder_fields = $this->buildPlaceholderFields($validated);
        $documentTemplate->save();

        return redirect()->route('admin.document-templates.index')->with('success', 'Document template updated successfully.');
    }

    public function destroy(DocumentTemplate $documentTemplate)
    {
        if ($documentTemplate->template_file_path && Storage::disk('public')->exists($documentTemplate->template_file_path)) {
            Storage::disk('public')->delete($documentTemplate->template_file_path);
        }
        $documentTemplate->delete();

        return redirect()->route('admin.document-templates.index')->with('success', 'Document template deleted.');
    }

    private function buildPlaceholderFields(array $validated): ?array
    {
        if (empty($validated['field_labels']) || !is_array($validated['field_labels'])) {
            return null;
        }

        $fields = [];
        $labels     = $validated['field_labels'];
        $names      = $validated['field_names'] ?? [];
        $types      = $validated['field_types'] ?? [];
        $required   = $validated['field_required'] ?? [];

        foreach ($labels as $i => $label) {
            if (empty($label)) continue;
            $name = $names[$i] ?? Str::slug($label, '_');
            if (empty($name)) $name = 'field_' . ($i + 1);
            $fields[] = [
                'label'    => trim($label),
                'name'     => trim($name),
                'type'     => in_array($types[$i] ?? null, ['text','textarea','date','select','file','number','email','tel']) ? $types[$i] : 'text',
                'required' => !empty($required[$i]),
            ];
        }

        return count($fields) > 0 ? $fields : null;
    }

    private function makeUniqueSlug(string $slug, int $ignoreId = null): string
    {
        $original = $slug;
        $counter = 1;
        $query = DocumentTemplate::where('slug', $slug);
        if ($ignoreId !== null) $query->where('id', '!=', $ignoreId);
        while ($query->exists()) {
            $slug = $original . '-' . $counter++;
            $query = DocumentTemplate::where('slug', $slug);
            if ($ignoreId !== null) $query->where('id', '!=', $ignoreId);
        }
        return $slug;
    }
}
