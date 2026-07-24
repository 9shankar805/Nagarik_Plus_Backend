<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SettingService;

class MaintenanceMode
{
    public function __construct(private SettingService $settingService) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Bypass admin portal and admin API
        if ($request->is('admin/*') || $request->is('api/*/admin/*')) {
            return $next($request);
        }

        // Check maintenance mode setting
        if ($this->settingService->get('maintenance_mode', false)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service is temporarily unavailable for maintenance.'
                ], 503);
            }
            // Optionally return a maintenance view here
            return response()->view('errors.503', [], 503);
        }

        return $next($request);
    }
}
