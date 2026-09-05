<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiLocalization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check header request and determine localizaton
        $local = ($request->hasHeader('Accept-Language')) ? $request->header('Accept-Language') : 'en';
        
        // Ensure only 'en' or 'ne' are set
        if (!in_array($local, ['en', 'ne'])) {
            $local = 'en';
        }
        
        // set laravel localization
        app()->setLocale($local);

        return $next($request);
    }
}
