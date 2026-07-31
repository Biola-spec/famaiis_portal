<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Supported locales
     */
    protected array $supported = ['en', 'fr', 'ar', 'es', 'sw', 'ha', 'yo', 'ig'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = 'en';

        if ($request->user()) {
            $userLang = $request->user()->language;
            if ($userLang && in_array($userLang, $this->supported)) {
                $locale = $userLang;
            }
        }

        App::setLocale($locale);

        return $next($request);
    }
}
