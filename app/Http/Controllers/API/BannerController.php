<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index(): JsonResponse
    {
        $banners = Banner::active()->get()->map(fn($b) => [
            'id'          => $b->id,
            'title'       => $b->title,
            'title_np'    => $b->title_np,
            'description' => $b->description,
            'image_url'   => $b->image_url,
            'link_type'   => $b->link_type,
            'link_value'  => $b->link_value,
        ]);

        return response()->json(['success' => true, 'data' => $banners]);
    }
}
