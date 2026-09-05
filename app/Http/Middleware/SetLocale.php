<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('lang') && in_array($request->query('lang'), ['en', 'ne'])) {
            $locale = $request->query('lang');
            Session::put('locale', $locale);
            session()->save();
        } else {
            $locale = Session::get('locale') ?? $request->cookie('locale') ?? config('app.locale', 'en');
        }

        if (in_array($locale, ['en', 'ne'])) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
