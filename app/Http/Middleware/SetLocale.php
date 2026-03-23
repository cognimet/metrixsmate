<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * SetLocale Middleware
 * 
 * Reads the user's preferred language from the session (or query param)
 * and sets the application locale accordingly.
 * Supports: en (English), hi (Hindi)
 */
class SetLocale
{
    /** @var array Supported locales */
    protected array $supported = ['en', 'hi'];

    public function handle(Request $request, Closure $next): Response
    {
        // Allow switching via ?lang=hi or ?lang=en
        if ($request->has('lang') && in_array($request->query('lang'), $this->supported)) {
            $locale = $request->query('lang');
            session(['locale' => $locale]);
        }

        $locale = session('locale', config('app.locale', 'en'));

        if (in_array($locale, $this->supported)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
