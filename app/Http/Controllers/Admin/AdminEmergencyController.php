<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmergencyContact;
use Illuminate\Http\Request;

class AdminEmergencyController extends Controller
{
    public function index()
    {
        $contacts = EmergencyContact::orderBy('sort_order')->orderBy('name')->paginate(25);
        return view('admin.emergency.index', compact('contacts'));
    }

    public function create()
    {
        return view('admin.emergency.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'name_np'     => 'nullable|string|max:255',
            'number'      => 'required|string|max:50',
            'description' => 'nullable|string',
            'category'    => 'required|in:police,ambulance,fire,disaster,health',
            'icon'        => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:50',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $data['is_active']  = $request->boolean('is_active');
        $data['sort_order'] = $request->input('sort_order', 0);

        EmergencyContact::create($data);
        return redirect()->route('admin.emergency.index')->with('success', 'Emergency contact created.');
    }

    public function show(EmergencyContact $emergency)
    {
        return view('admin.emergency.show', compact('emergency'));
    }

    public function edit(EmergencyContact $emergency)
    {
        return view('admin.emergency.edit', compact('emergency'));
    }

    public function update(Request $request, EmergencyContact $emergency)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'name_np'     => 'nullable|string|max:255',
            'number'      => 'required|string|max:50',
            'description' => 'nullable|string',
            'category'    => 'required|in:police,ambulance,fire,disaster,health',
            'icon'        => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:50',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $data['is_active']  = $request->boolean('is_active');
        $data['sort_order'] = $request->input('sort_order', 0);

        $emergency->update($data);
        return redirect()->route('admin.emergency.index')->with('success', 'Emergency contact updated.');
    }

    public function destroy(EmergencyContact $emergency)
    {
        $emergency->delete();
        return back()->with('success', 'Emergency contact deleted.');
    }
}
