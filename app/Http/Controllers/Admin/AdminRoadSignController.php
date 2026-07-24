<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoadSign;
use Illuminate\Http\Request;

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
            'color_code' => 'nullable|string|max:20',
            'is_active'  => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

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
            'color_code' => 'nullable|string|max:20',
            'is_active'  => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $roadSign->update($data);
        return redirect()->route('admin.road-signs.index')->with('success', 'Road sign updated.');
    }

    public function destroy(RoadSign $roadSign)
    {
        $roadSign->delete();
        return back()->with('success', 'Road sign deleted.');
    }
}
