<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VitalEvent;
use Illuminate\Http\Request;

class AdminVitalEventController extends Controller
{
    public function index()
    {
        $events = VitalEvent::latest()->paginate(15);
        return view('admin.vital_events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.vital_events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_np' => 'nullable|string|max:255',
            'bg_color' => 'nullable|string|max:255',
            'image_asset' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');
        VitalEvent::create($validated);

        return redirect()->route('admin.vital-events.index')->with('success', 'Vital Event card created successfully.');
    }

    public function edit(VitalEvent $vitalEvent)
    {
        return view('admin.vital_events.edit', compact('vitalEvent'));
    }

    public function update(Request $request, VitalEvent $vitalEvent)
    {
        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_np' => 'nullable|string|max:255',
            'bg_color' => 'nullable|string|max:255',
            'image_asset' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $vitalEvent->update($validated);

        return redirect()->route('admin.vital-events.index')->with('success', 'Vital Event card updated successfully.');
    }

    public function destroy(VitalEvent $vitalEvent)
    {
        $vitalEvent->delete();
        return redirect()->route('admin.vital-events.index')->with('success', 'Vital Event card deleted successfully.');
    }
}
