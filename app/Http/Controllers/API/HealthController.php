<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Exception;

class HealthController extends Controller
{
    public function check(): JsonResponse
    {
        $services = [
            'app'       => true,
            'database'  => true,
        ];

        $details = [
            'app' => [
                'name'       => config('app.name', 'Nagarik Plus'),
                'env'        => config('app.env'),
                'debug'      => config('app.debug'),
                'version'    => '1.0.0',
                'timezone'   => config('app.timezone'),
                'locale'     => config('app.locale'),
            ],
            'database' => [
                'connection' => config('database.default'),
                'reachable'  => false,
            ],
            'timestamp' => now()->toIso8601String(),
        ];

        try {
            DB::connection()->getPdo();
            $details['database']['reachable'] = true;
        } catch (Exception $e) {
            $services['database'] = false;
            $details['database']['error'] = $e->getMessage();
        }

        $overallStatus = !in_array(false, $services, true);

        return response()->json([
            'success' => $overallStatus,
            'status'  => $overallStatus ? 'ok' : 'degraded',
            'services' => $services,
            'details'  => $details,
        ], $overallStatus ? 200 : 503);
    }
}
