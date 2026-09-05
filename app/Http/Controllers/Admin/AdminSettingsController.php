<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SettingService;
use Illuminate\Support\Facades\Cache;

class AdminSettingsController extends Controller
{
    public function __construct(private SettingService $settingService) {}

    public function index()
    {
        $settings = [
            'app_name'  => config('app.name'),
            'app_env'   => config('app.env'),
            'app_debug' => config('app.debug'),
            'max_upload_mb' => $this->settingService->get('max_upload_mb', 10),
            'maintenance_mode' => $this->settingService->get('maintenance_mode', false),
            'fcm_server_key' => config('services.fcm.server_key'),
            'fcm_project_id' => config('services.fcm.project_id'),
            'help_faq' => $this->settingService->get('help_faq', ''),
            'privacy_policy' => $this->settingService->get('privacy_policy', ''),
            'about_app' => $this->settingService->get('about_app', ''),
            'rate_app_url' => $this->settingService->get('rate_app_url', ''),
        ];
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'max_upload_mb' => 'nullable|integer|min:1|max:100',
            'maintenance_mode' => 'nullable|boolean',
            'help_faq' => 'nullable|string',
            'privacy_policy' => 'nullable|string',
            'about_app' => 'nullable|string',
            'rate_app_url' => 'nullable|url',
        ]);

        if ($request->has('max_upload_mb')) {
            $this->settingService->set('max_upload_mb', $request->max_upload_mb);
        }
        
        $this->settingService->set('maintenance_mode', $request->boolean('maintenance_mode'));
        $this->settingService->set('help_faq', $request->input('help_faq'));
        $this->settingService->set('privacy_policy', $request->input('privacy_policy'));
        $this->settingService->set('about_app', $request->input('about_app'));
        $this->settingService->set('rate_app_url', $request->input('rate_app_url'));

        // Clear cache if maintenance mode is toggled
        Cache::flush();

        return back()->with('success', 'Settings saved successfully.');
    }
}
