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
        ];
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'max_upload_mb' => 'nullable|integer|min:1|max:100',
            'maintenance_mode' => 'nullable|boolean',
        ]);

        if ($request->has('max_upload_mb')) {
            $this->settingService->set('max_upload_mb', $request->max_upload_mb);
        }
        
        $this->settingService->set('maintenance_mode', $request->boolean('maintenance_mode'));

        // Clear cache if maintenance mode is toggled
        Cache::flush();

        return back()->with('success', 'Settings saved successfully.');
    }
}
