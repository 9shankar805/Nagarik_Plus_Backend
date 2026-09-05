<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class AdminBannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->latest()->paginate(15);
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'title_np'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_url'   => 'nullable|string|max:500',
            'link_type'   => 'required|in:none,news,service,external,document',
            'link_value'  => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer|min:0',
            'starts_at'   => 'nullable|date',
            'ends_at'     => 'nullable|date|after_or_equal:starts_at',
        ]);

        $validated['is_active']  = $request->has('is_active');
        $validated['sort_order'] = $request->input('sort_order', 0);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/banners'), $imageName);
            $validated['image_url'] = '/assets/banners/' . $imageName;
        }

        // Remove image from validated array since we only need image_url
        unset($validated['image']);

        Banner::create($validated);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'title_np'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_url'   => 'nullable|string|max:500',
            'link_type'   => 'required|in:none,news,service,external,document',
            'link_value'  => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer|min:0',
            'starts_at'   => 'nullable|date',
            'ends_at'     => 'nullable|date|after_or_equal:starts_at',
        ]);

        $validated['is_active']  = $request->has('is_active');
        $validated['sort_order'] = $request->input('sort_order', 0);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($banner->image_url && file_exists(public_path($banner->image_url))) {
                unlink(public_path($banner->image_url));
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/banners'), $imageName);
            $validated['image_url'] = '/assets/banners/' . $imageName;
        }

        // Remove image from validated array since we only need image_url
        unset($validated['image']);

        $banner->update($validated);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
    }
}
