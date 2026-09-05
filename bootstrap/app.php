<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // JSON responses for API
        $middleware->statefulApi();
        
        $middleware->api(prepend: [
            \App\Http\Middleware\ApiLocalization::class,
        ]);

        // Apply maintenance mode globally
        $middleware->use([\App\Http\Middleware\MaintenanceMode::class]);

        // Locale middleware for web group
        $middleware->appendToGroup('web', \App\Http\Middleware\SetLocale::class);

        // Admin portal alias
        $middleware->alias([
            'admin'        => \App\Http\Middleware\AdminMiddleware::class,
            'not.banned'   => \App\Http\Middleware\EnsureNotBanned::class,
            'kyc.verified' => \App\Http\Middleware\KycVerified::class,
        ]);

        // Redirect unauthenticated guests to /login
        $middleware->redirectTo(guests: '/login');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle 419 Page Expired (Token Mismatch) gracefully by redirecting back with a fresh session
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, Request $request) {
            if ($request->expectsJson() || $request->header('X-Livewire')) {
                return response()->json(['message' => 'Session expired. Refreshing page...'], 419);
            }
            return redirect()->back()->with('error', 'Session expired. Page has been refreshed with a new session token.');
        });

        // Return JSON errors for API requests
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed.',
                        'errors'  => $e->errors(),
                    ], 422);
                }

                if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Resource not found.',
                    ], 404);
                }

                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthenticated. Please login.',
                    ], 401);
                }

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Server error.',
                ], $status >= 400 ? $status : 500);
            }
        });
    })->create();
