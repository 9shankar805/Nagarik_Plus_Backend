<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Http\Request;

class AdminHospitalController extends Controller
{
    public function index()
    {
        $hospitals = Hospital::latest()->paginate(15);
        return view('admin.hospitals.index', compact('hospitals'));
    }

    public function create()
    {
        return view('admin.hospitals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_np' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'address_np' => 'nullable|string|max:255',
            'phone' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $validated['is_active'] = $request->has('is_active');
        Hospital::create($validated);

        return redirect()->route('admin.hospitals.index')->with('success', 'Hospital created successfully.');
    }

    public function edit(Hospital $hospital)
    {
        return view('admin.hospitals.edit', compact('hospital'));
    }

    public function update(Request $request, Hospital $hospital)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_np' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'address_np' => 'nullable|string|max:255',
            'phone' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $hospital->update($validated);

        return redirect()->route('admin.hospitals.index')->with('success', 'Hospital updated successfully.');
    }

    public function destroy(Hospital $hospital)
    {
        $hospital->delete();
        return redirect()->route('admin.hospitals.index')->with('success', 'Hospital deleted successfully.');
    }
}
