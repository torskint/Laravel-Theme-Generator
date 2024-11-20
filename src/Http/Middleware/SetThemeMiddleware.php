<?php

namespace Torskint\ThemeGenerator\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class SetThemeMiddleware
{
    public function handle($request, Closure $next)
    {
        // Vérifiez si le paramètre 'theme' est présent dans l'URL
        if ( $request->filled('theme') ) {
            $theme = 'theme_' . $request->get('theme');
            Session::put('theme', $theme);
        }

        return $next($request);
    }
}
