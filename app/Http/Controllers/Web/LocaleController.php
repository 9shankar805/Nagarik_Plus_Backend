<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    /**
     * Switch language locale.
     */
    public function switch(string $lang): RedirectResponse
    {
        if (in_array($lang, ['en', 'ne'])) {
            Session::put('locale', $lang);
            session()->save();

            $referer = request()->headers->get('referer');
            if ($referer) {
                $parse = parse_url($referer);
                $path = $parse['path'] ?? '/';
                return redirect($path . '?lang=' . $lang)->withCookie(cookie()->forever('locale', $lang));
            }

            return redirect('/?lang=' . $lang);
        }

        return redirect()->back();
    }
}
