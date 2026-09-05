<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoadSign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminRoadSignController extends Controller
{
    public function index()
    {
        $signs = RoadSign::latest()->paginate(25);
        return view('admin.road_signs.index', compact('signs'));
    }

    public function create()
    {
        return view('admin.road_signs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'name_np'    => 'nullable|string|max:255',
            'meaning'    => 'required|string',
            'meaning_np' => 'nullable|string',
            'category'   => 'required|in:warning,mandatory,informational',
            'image_url'  => 'nullable|url',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'color_code' => 'nullable|string|max:20',
            'is_active'  => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('learning/road_signs', 'public');
            $data['image_url'] = Storage::url($path);
        }

        RoadSign::create($data);
        return redirect()->route('admin.road-signs.index')->with('success', 'Road sign created.');
    }

    public function show(RoadSign $roadSign)
    {
        return view('admin.road_signs.show', compact('roadSign'));
    }

    public function edit(RoadSign $roadSign)
    {
        return view('admin.road_signs.edit', compact('roadSign'));
    }

    public function update(Request $request, RoadSign $roadSign)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'name_np'    => 'nullable|string|max:255',
            'meaning'    => 'required|string',
            'meaning_np' => 'nullable|string',
            'category'   => 'required|in:warning,mandatory,informational',
            'image_url'  => 'nullable|url',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'color_code' => 'nullable|string|max:20',
            'is_active'  => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image_file')) {
            if ($roadSign->image_url && str_starts_with($roadSign->image_url, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $roadSign->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image_file')->store('learning/road_signs', 'public');
            $data['image_url'] = Storage::url($path);
        }

        $roadSign->update($data);
        return redirect()->route('admin.road-signs.index')->with('success', 'Road sign updated.');
    }

    public function destroy(RoadSign $roadSign)
    {
        $roadSign->delete();
        return back()->with('success', 'Road sign deleted.');
    }
}
