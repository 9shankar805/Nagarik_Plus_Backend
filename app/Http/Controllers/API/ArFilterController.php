<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ArFilter;
use Illuminate\Http\Request;

class ArFilterController extends Controller
{
    /**
     * Display a listing of active AR filters.
     */
    public function index()
    {
        $filters = ArFilter::where('is_active', true)->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $filters
        ]);
    }
}
