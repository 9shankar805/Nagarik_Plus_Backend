<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminLearningCategoryController extends Controller
{
    public function index()
    {
        $categories = LearningCategory::withCount(['chapters', 'mockTests', 'competitions'])
            ->ordered()
            ->paginate(20);

        return view('admin.learning.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.learning.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_en'       => 'required|string|max:150',
            'name_np'       => 'nullable|string|max:150',
            'description_en'=> 'nullable|string',
            'description_np'=> 'nullable|string',
            'icon'          => 'nullable|string|max:50',
            'color_code'    => 'nullable|string|max:10',
            'display_order' => 'nullable|integer|min:0',
            'banner_file'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $slug = Str::slug($request->name_en);
        $base = $slug;
        $i = 1;
        while (LearningCategory::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $bannerUrl = null;
        if ($request->hasFile('banner_file')) {
            $path = $request->file('banner_file')->store('learning/categories', 'public');
            $bannerUrl = Storage::url($path);
        }

        LearningCategory::create([
            'slug'          => $slug,
            'name_en'       => $request->name_en,
            'name_np'       => $request->name_np,
            'description_en'=> $request->description_en,
            'description_np'=> $request->description_np,
            'icon'          => $request->icon,
            'color_code'    => $request->color_code,
            'banner_url'    => $bannerUrl,
            'display_order' => $request->display_order ?? 0,
            'is_active'     => $request->has('is_active'),
        ]);

        return redirect()->route('admin.learning.categories.index')
            ->with('success', 'Learning category created successfully.');
    }

    public function edit(LearningCategory $category)
    {
        return view('admin.learning.categories.edit', compact('category'));
    }

    public function update(Request $request, LearningCategory $category)
    {
        $request->validate([
            'name_en'       => 'required|string|max:150',
            'name_np'       => 'nullable|string|max:150',
            'description_en'=> 'nullable|string',
            'description_np'=> 'nullable|string',
            'icon'          => 'nullable|string|max:50',
            'color_code'    => 'nullable|string|max:10',
            'display_order' => 'nullable|integer|min:0',
            'banner_file'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $bannerUrl = $category->banner_url;
        if ($request->hasFile('banner_file')) {
            $path = $request->file('banner_file')->store('learning/categories', 'public');
            $bannerUrl = Storage::url($path);
        }

        $category->update([
            'name_en'       => $request->name_en,
            'name_np'       => $request->name_np,
            'description_en'=> $request->description_en,
            'description_np'=> $request->description_np,
            'icon'          => $request->icon,
            'color_code'    => $request->color_code,
            'banner_url'    => $bannerUrl,
            'display_order' => $request->display_order ?? 0,
            'is_active'     => $request->has('is_active'),
        ]);

        return redirect()->route('admin.learning.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(LearningCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}
