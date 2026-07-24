<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advisor;
use App\Models\Consultation;
use Illuminate\Http\Request;

class AdminAdvisorController extends Controller
{
    public function index()
    {
        $advisors = Advisor::latest()->paginate(15);
        return view('admin.advisors.index', compact('advisors'));
    }

    public function create()
    {
        return view('admin.advisors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'title_np' => 'nullable|string|max:255',
            'category' => 'required|string|max:255',
            'rating' => 'nullable|numeric',
            'consultation_fee_chat' => 'required|numeric',
            'consultation_fee_call' => 'required|numeric',
            'experience_years' => 'required|integer',
            'bio_en' => 'nullable|string',
            'bio_np' => 'nullable|string',
            'location' => 'nullable|string|max:255',
        ]);

        $validated['is_online'] = $request->has('is_online');
        $validated['is_verified'] = $request->has('is_verified');
        Advisor::create($validated);

        return redirect()->route('admin.advisors.index')->with('success', 'Advisor created successfully.');
    }

    public function edit(Advisor $advisor)
    {
        return view('admin.advisors.edit', compact('advisor'));
    }

    public function update(Request $request, Advisor $advisor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'title_np' => 'nullable|string|max:255',
            'category' => 'required|string|max:255',
            'rating' => 'nullable|numeric',
            'consultation_fee_chat' => 'required|numeric',
            'consultation_fee_call' => 'required|numeric',
            'experience_years' => 'required|integer',
            'bio_en' => 'nullable|string',
            'bio_np' => 'nullable|string',
            'location' => 'nullable|string|max:255',
        ]);

        $validated['is_online'] = $request->has('is_online');
        $validated['is_verified'] = $request->has('is_verified');
        $advisor->update($validated);

        return redirect()->route('admin.advisors.index')->with('success', 'Advisor updated successfully.');
    }

    public function destroy(Advisor $advisor)
    {
        $advisor->delete();
        return redirect()->route('admin.advisors.index')->with('success', 'Advisor deleted successfully.');
    }

    public function consultations()
    {
        $consultations = Consultation::with(['user', 'advisor'])->latest()->paginate(15);
        return view('admin.advisors.consultations', compact('consultations'));
    }
}
