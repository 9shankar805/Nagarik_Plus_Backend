<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialService;
use Illuminate\Http\Request;

class AdminSocialServiceController extends Controller
{
    public function index()
    {
        $services = SocialService::latest()->paginate(15);
        return view('admin.social_services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.social_services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_np' => 'nullable|string|max:255',
            'subtitle_en' => 'nullable|string|max:255',
            'subtitle_np' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');
        SocialService::create($validated);

        return redirect()->route('admin.social-services.index')->with('success', 'Social Service card created successfully.');
    }

    public function edit(SocialService $socialService)
    {
        return view('admin.social_services.edit', compact('socialService'));
    }

    public function update(Request $request, SocialService $socialService)
    {
        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_np' => 'nullable|string|max:255',
            'subtitle_en' => 'nullable|string|max:255',
            'subtitle_np' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $socialService->update($validated);

        return redirect()->route('admin.social-services.index')->with('success', 'Social Service card updated successfully.');
    }

    public function destroy(SocialService $socialService)
    {
        $socialService->delete();
        return redirect()->route('admin.social-services.index')->with('success', 'Social Service card deleted successfully.');
    }
}
