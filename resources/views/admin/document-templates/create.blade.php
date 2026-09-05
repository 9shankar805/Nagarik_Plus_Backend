@extends('admin.layouts.app')

@section('title', 'Add Document Template')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.document-templates.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">← Back to Templates</a>
    <h1 class="text-2xl font-bold text-gray-900 mt-2">Add New Document Template</h1>
    <p class="text-sm text-gray-600">Upload a PDF/DOCX form and define the fields a citizen must fill out.</p>
</div>

<form action="{{ route('admin.document-templates.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @include('admin.document-templates.partials.form')
        <div class="pt-6 mt-6 border-t border-gray-100 flex gap-3">
            <button type="submit"
                    class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                Save Template
            </button>
            <a href="{{ route('admin.document-templates.index') }}"
               class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                Cancel
            </a>
        </div>
    </div>
</form>
@endsection
