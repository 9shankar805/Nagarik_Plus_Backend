<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Grievance;
use App\Models\GrievanceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GrievanceController extends Controller
{
    public function categories()
    {
        $categories = GrievanceCategory::where('is_active', true)->get();
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function index(Request $request)
    {
        $grievances = $request->user()->grievances()->with('category')->latest()->get();
        return response()->json([
            'success' => true,
            'data' => $grievances
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'grievance_category_id' => 'required|exists:grievance_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'photo' => 'nullable|image|max:5120', // 5MB max
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('grievances', 'public');
        }

        $grievance = $request->user()->grievances()->create([
            'grievance_category_id' => $request->grievance_category_id,
            'title' => $request->title,
            'description' => $request->description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo_path' => $photoPath,
            'status' => 'open'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Grievance submitted successfully.',
            'data' => $grievance
        ], 201);
    }
}
