<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CitizenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminServiceController extends Controller
{
    public function index()
    {
        $services = CitizenService::orderBy('sort_order')->orderBy('title')->paginate(20);
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug'                => 'required|string|max:255|unique:citizen_services,slug',
            'title'               => 'required|string|max:255',
            'title_np'            => 'nullable|string|max:255',
            'description'         => 'required|string',
            'description_np'      => 'nullable|string',
            'category'            => 'required|in:identity,vehicle,finance,business,legal',
            'eligibility'         => 'nullable|string',
            'required_documents'  => 'nullable|string',
            'application_steps'   => 'nullable|string',
            'fee'                 => 'nullable|string|max:255',
            'processing_time'     => 'nullable|string|max:255',
            'faqs'                => 'nullable|string',
            'official_url'        => 'nullable|url',
            'video_url'           => 'nullable|url',
            'video_file'          => 'nullable|mimes:mp4,mov,avi,webm|max:102400',
            'sort_order'          => 'integer|min:0',
            'is_active'           => 'boolean',
        ]);

        $data['is_active']  = $request->boolean('is_active');
        $data['sort_order'] = $request->input('sort_order', 0);

        // Decode JSON textarea fields
        foreach (['required_documents', 'application_steps', 'faqs'] as $field) {
            if (!empty($data[$field])) {
                $decoded = json_decode($data[$field], true);
                $data[$field] = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
            } else {
                $data[$field] = null;
            }
        }
        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('services/videos', 'public');
            $data['video_url'] = Storage::url($path);
        }

        CitizenService::create($data);
        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }

    public function show(CitizenService $service)
    {
        return view('admin.services.show', compact('service'));
    }

    public function edit(CitizenService $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, CitizenService $service)
    {
        $data = $request->validate([
            'slug'                => 'required|string|max:255|unique:citizen_services,slug,' . $service->id,
            'title'               => 'required|string|max:255',
            'title_np'            => 'nullable|string|max:255',
            'description'         => 'required|string',
            'description_np'      => 'nullable|string',
            'category'            => 'required|in:identity,vehicle,finance,business,legal',
            'eligibility'         => 'nullable|string',
            'required_documents'  => 'nullable|string',
            'application_steps'   => 'nullable|string',
            'fee'                 => 'nullable|string|max:255',
            'processing_time'     => 'nullable|string|max:255',
            'faqs'                => 'nullable|string',
            'official_url'        => 'nullable|url',
            'video_url'           => 'nullable|url',
            'video_file'          => 'nullable|mimes:mp4,mov,avi,webm|max:102400',
            'sort_order'          => 'integer|min:0',
            'is_active'           => 'boolean',
        ]);

        $data['is_active']  = $request->boolean('is_active');
        $data['sort_order'] = $request->input('sort_order', 0);

        foreach (['required_documents', 'application_steps', 'faqs'] as $field) {
            if (!empty($data[$field])) {
                $decoded = json_decode($data[$field], true);
                $data[$field] = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
            } else {
                $data[$field] = null;
            }
        }
        if ($request->hasFile('video_file')) {
            if ($service->video_url && str_starts_with($service->video_url, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $service->video_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('video_file')->store('services/videos', 'public');
            $data['video_url'] = Storage::url($path);
        }

        $service->update($data);
        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(CitizenService $service)
    {
        $service->delete();
        return back()->with('success', 'Service deleted.');
    }
}
