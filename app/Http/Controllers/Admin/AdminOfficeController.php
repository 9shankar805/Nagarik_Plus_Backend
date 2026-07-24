<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Office;
use Illuminate\Http\Request;

class AdminOfficeController extends Controller
{
    public function index()
    {
        $offices = Office::latest()->paginate(20);
        return view('admin.offices.index', compact('offices'));
    }

    public function create()
    {
        return view('admin.offices.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'name_np'      => 'nullable|string|max:255',
            'category'     => 'required|in:passport,transport,tax,municipality,police,ward,dao',
            'address'      => 'required|string|max:500',
            'district'     => 'required|string|max:100',
            'province'     => 'required|string|max:100',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
            'phone'        => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'website'      => 'nullable|url|max:255',
            'office_hours' => 'nullable|string|max:255',
            'is_active'    => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        Office::create($data);
        return redirect()->route('admin.offices.index')->with('success', 'Office created successfully.');
    }

    public function show(Office $office)
    {
        return view('admin.offices.show', compact('office'));
    }

    public function edit(Office $office)
    {
        return view('admin.offices.edit', compact('office'));
    }

    public function update(Request $request, Office $office)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'name_np'      => 'nullable|string|max:255',
            'category'     => 'required|in:passport,transport,tax,municipality,police,ward,dao',
            'address'      => 'required|string|max:500',
            'district'     => 'required|string|max:100',
            'province'     => 'required|string|max:100',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
            'phone'        => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'website'      => 'nullable|url|max:255',
            'office_hours' => 'nullable|string|max:255',
            'is_active'    => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $office->update($data);
        return redirect()->route('admin.offices.index')->with('success', 'Office updated successfully.');
    }

    public function destroy(Office $office)
    {
        $office->delete();
        return back()->with('success', 'Office deleted.');
    }
}
