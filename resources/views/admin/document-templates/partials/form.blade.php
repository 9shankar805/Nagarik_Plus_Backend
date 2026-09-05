@php
$tpl       = $tpl ?? null;
$categories = $categories ?? \App\Models\DocumentTemplate::CATEGORIES;
$fields    = old('field_labels', $tpl?->placeholder_fields ?? [['label' => '', 'name' => '', 'type' => 'text', 'required' => false]]);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
        <input type="text" name="title" required value="{{ old('title', $tpl?->title) }}"
               placeholder="e.g. Driving License Application Form"
               class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border">
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">URL Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $tpl?->slug) }}"
               placeholder="auto-generated from title if left blank"
               class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border">
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
        <select name="category" required class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border bg-white">
            <option value="" disabled selected>Select a category...</option>
            @foreach($categories as $key => $label)
                <option value="{{ $key }}" {{ old('category', $tpl?->category) === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex gap-3 items-end">
        <div class="flex-1">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Sort Order</label>
            <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $tpl?->sort_order ?? 0) }}"
                   class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border">
        </div>
        <label class="flex items-center gap-2 cursor-pointer mb-2">
            <input type="checkbox" name="is_active" value="1" class="w-4 h-4 text-blue-600 rounded" {{ old('is_active', $tpl?->is_active ?? true) ? 'checked' : '' }}>
            <span class="text-sm font-medium text-gray-700">Active</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer mb-2">
            <input type="checkbox" name="is_featured" value="1" class="w-4 h-4 text-amber-500 rounded" {{ old('is_featured', $tpl?->is_featured ?? false) ? 'checked' : '' }}>
            <span class="text-sm font-medium text-gray-700">Featured</span>
        </label>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
        <textarea name="description" rows="2"
                  placeholder="Short description explaining what this template/form is for, when a citizen needs it."
                  class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border">{{ old('description', $tpl?->description) }}</textarea>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Preview Image URL</label>
        <input type="url" name="preview_image" value="{{ old('preview_image', $tpl?->preview_image) }}"
               placeholder="https://example.com/preview.jpg (shown in the citizen app as a thumbnail)"
               class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Template File (PDF / DOCX / XLSX etc.)</label>
        @if($tpl?->template_file_path)
            <div class="mb-2 flex items-center gap-3 text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <a href="{{ Storage::disk('public')->url($tpl->template_file_path) }}" target="_blank"
                   class="text-blue-600 hover:underline font-medium">{{ $tpl->template_file_name }}</a>
                <span class="text-xs text-gray-500 ml-auto">{{ number_format($tpl->template_file_size / 1024, 1) }} KB</span>
                <label class="flex items-center gap-1.5 cursor-pointer ml-4 text-red-600 hover:text-red-700 text-xs font-medium">
                    <input type="checkbox" name="remove_file" value="1" class="w-3.5 h-3.5 text-red-600 rounded">
                    Remove file
                </label>
            </div>
        @endif
        <input type="file" name="template_file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.svg,.ai,.psd,.zip"
               class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border bg-white file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        <p class="text-xs text-gray-500 mt-1">Max 20 MB. Supported: PDF, Word (DOC/DOCX), Excel (XLS/XLSX), PowerPoint, Text, CSV, Images (SVG/AI/PSD), ZIP.</p>
    </div>

    <div class="md:col-span-2">
        <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-semibold text-gray-700 mb-0">Placeholder Fields <span class="text-gray-400 text-xs font-normal">(shown to the citizen app user when filling out this form)</span></label>
            <button type="button" id="add-field-btn"
                    class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-200 transition flex items-center gap-1">
                + Add Field
            </button>
        </div>

        <div id="placeholder-fields" class="border border-gray-200 rounded-lg p-3 bg-gray-50/30 space-y-2">
            @foreach($fields as $idx => $f)
                <div class="placeholder-row grid grid-cols-12 gap-2 items-end bg-white border border-gray-100 rounded-lg p-2">
                    <input type="hidden" name="field_required[{{ $idx }}]" value="0">
                    <div class="col-span-4">
                        <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Label</label>
                        <input type="text" name="field_labels[]" value="{{ old('field_labels.'.$idx, $f['label'] ?? '') }}"
                               placeholder="e.g. Full Name" class="w-full border-gray-300 rounded text-sm px-2 py-1.5 border focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="col-span-3">
                        <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Name (slug)</label>
                        <input type="text" name="field_names[]" value="{{ old('field_names.'.$idx, $f['name'] ?? '') }}"
                               placeholder="e.g. full_name" class="w-full border-gray-300 rounded text-sm px-2 py-1.5 border focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Type</label>
                        <select name="field_types[]" class="w-full border-gray-300 rounded text-sm px-2 py-1.5 border bg-white focus:ring-blue-500 focus:border-blue-500">
                            @php
                                $ft = old('field_types.'.$idx, $f['type'] ?? 'text');
                            @endphp
                            @foreach(['text','textarea','number','email','tel','date','select','file'] as $type)
                                <option value="{{ $type }}" {{ $ft === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2 text-center">
                        <label class="inline-flex items-center gap-1 cursor-pointer">
                            <input type="checkbox" name="field_required[{{ $idx }}]" value="1" class="w-4 h-4 text-blue-600 rounded"
                                {{ old('field_required.'.$idx, ($f['required'] ?? false) ? '1' : '') === '1' || (is_bool(old('field_required.'.$idx)) && old('field_required.'.$idx)) ? 'checked' : '' }}>
                            <span class="text-xs font-medium text-gray-700">Required</span>
                        </label>
                    </div>
                    <div class="col-span-1 text-right">
                        <button type="button" class="remove-field-btn text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50" title="Remove field">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Guidelines / Tips for Citizen</label>
        <textarea name="guidelines" rows="4"
                  placeholder="Explain how to fill this form, which documents are required, where to submit it, any fees, office visit notes, etc. Example: 'Bring 2 passport photos, Citizenship card, and Rs. 500 fee. Submit at nearest Transport Management Office.'"
                  class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border">{{ old('guidelines', $tpl?->guidelines) }}</textarea>
    </div>
</div>

@section('scripts')
<script>
(function () {
    const container = document.getElementById('placeholder-fields');
    const addBtn    = document.getElementById('add-field-btn');
    function reindex() {
        container.querySelectorAll('.placeholder-row').forEach((row, i) => {
            row.querySelectorAll('[name^="field_"]').forEach(input => {
                const m = input.name.match(/^(\w+?)\[(\d*)\](\[(\d+)\])?$/);
                if (!m) return;
                if (m[3]) {
                    input.name = m[1] + '[' + i + '][' + m[4] + ']';
                } else if (input.name.includes('[]')) {
                    // array[] style — leave as is for non-keyed fields
                } else {
                    input.name = m[1] + '[' + i + ']';
                }
            });
        });
    }
    function attachRemove(btn) {
        btn.addEventListener('click', () => {
            const rows = container.querySelectorAll('.placeholder-row');
            if (rows.length > 1) {
                btn.closest('.placeholder-row').remove();
                reindex();
            }
        });
    }
    container.querySelectorAll('.remove-field-btn').forEach(attachRemove);
    addBtn.addEventListener('click', () => {
        const idx = container.querySelectorAll('.placeholder-row').length;
        const tpl = `
            <div class="placeholder-row grid grid-cols-12 gap-2 items-end bg-white border border-gray-100 rounded-lg p-2">
                <input type="hidden" name="field_required[${idx}]" value="0">
                <div class="col-span-4">
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Label</label>
                    <input type="text" name="field_labels[]" placeholder="e.g. Date of Birth" class="w-full border-gray-300 rounded text-sm px-2 py-1.5 border focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="col-span-3">
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Name (slug)</label>
                    <input type="text" name="field_names[]" placeholder="e.g. date_of_birth" class="w-full border-gray-300 rounded text-sm px-2 py-1.5 border focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Type</label>
                    <select name="field_types[]" class="w-full border-gray-300 rounded text-sm px-2 py-1.5 border bg-white focus:ring-blue-500 focus:border-blue-500">
                        <option value="text">Text</option>
                        <option value="textarea">Textarea</option>
                        <option value="number">Number</option>
                        <option value="email">Email</option>
                        <option value="tel">Tel</option>
                        <option value="date">Date</option>
                        <option value="select">Select</option>
                        <option value="file">File</option>
                    </select>
                </div>
                <div class="col-span-2 text-center">
                    <label class="inline-flex items-center gap-1 cursor-pointer">
                        <input type="checkbox" name="field_required[${idx}]" value="1" class="w-4 h-4 text-blue-600 rounded">
                        <span class="text-xs font-medium text-gray-700">Required</span>
                    </label>
                </div>
                <div class="col-span-1 text-right">
                    <button type="button" class="remove-field-btn text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50" title="Remove field">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', tpl);
        attachRemove(container.querySelector('.placeholder-row:last-child .remove-field-btn'));
    });
})();
</script>
@endsection
