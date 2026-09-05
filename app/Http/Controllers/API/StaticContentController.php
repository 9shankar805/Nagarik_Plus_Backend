<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;

class StaticContentController extends Controller
{
    public function __construct(private SettingService $settingService) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'help_faq' => $this->settingService->get('help_faq', ''),
                'privacy_policy' => $this->settingService->get('privacy_policy', ''),
                'about_app' => $this->settingService->get('about_app', ''),
                'rate_app_url' => $this->settingService->get('rate_app_url', ''),
            ]
        ]);
    }
}
