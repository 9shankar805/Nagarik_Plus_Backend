<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminArFilterController extends Controller
{
    public function index()
    {
        $filters = ArFilter::latest()->paginate(15);
        return view('admin.ar_filters.index', compact('filters'));
    }

    public function create()
    {
        return view('admin.ar_filters.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'engine' => 'required|string|max:100',
            'filter_file' => 'nullable|file|max:50000', // e.g. .deepar, .zip
            'filter_file_url_text' => 'nullable|url|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5000',
            'is_active' => 'nullable|boolean',
        ]);

        $filterUrl = $request->filter_file_url_text;
        if ($request->hasFile('filter_file')) {
            $path = $request->file('filter_file')->store('ar_filters/files', 'public');
            $filterUrl = Storage::url($path);
        }

        $thumbnailUrl = null;
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('ar_filters/thumbnails', 'public');
            $thumbnailUrl = Storage::url($path);
        }

        ArFilter::create([
            'title' => $validated['title'],
            'engine' => $validated['engine'],
            'filter_file_url' => $filterUrl,
            'thumbnail_url' => $thumbnailUrl,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.ar-filters.index')->with('success', 'AR Filter created successfully.');
    }

    public function edit(ArFilter $arFilter)
    {
        return view('admin.ar_filters.edit', compact('arFilter'));
    }

    public function update(Request $request, ArFilter $arFilter)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'engine' => 'required|string|max:100',
            'filter_file' => 'nullable|file|max:50000',
            'filter_file_url_text' => 'nullable|url|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5000',
            'is_active' => 'nullable|boolean',
        ]);

        $filterUrl = $arFilter->filter_file_url;
        if ($request->hasFile('filter_file')) {
            $path = $request->file('filter_file')->store('ar_filters/files', 'public');
            $filterUrl = Storage::url($path);
        } elseif ($request->filled('filter_file_url_text')) {
            $filterUrl = $request->filter_file_url_text;
        }

        $thumbnailUrl = $arFilter->thumbnail_url;
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('ar_filters/thumbnails', 'public');
            $thumbnailUrl = Storage::url($path);
        }

        $arFilter->update([
            'title' => $validated['title'],
            'engine' => $validated['engine'],
            'filter_file_url' => $filterUrl,
            'thumbnail_url' => $thumbnailUrl,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.ar-filters.index')->with('success', 'AR Filter updated successfully.');
    }

    public function destroy(ArFilter $arFilter)
    {
        $arFilter->delete();
        return redirect()->route('admin.ar-filters.index')->with('success', 'AR Filter deleted successfully.');
    }
}
