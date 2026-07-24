<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CitizenService;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    public function index()
    {
        $guides = CitizenService::active()->get();
        return view('user.guides', compact('guides'));
    }

    public function show($slug)
    {
        $guide = CitizenService::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('user.guide-detail', compact('guide'));
    }
}
