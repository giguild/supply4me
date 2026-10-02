<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventBrowserCaching
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $path = '/'.ltrim($request->path(), '/');

        if (str_starts_with($path, '/build/')) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');

            return $response;
        }

        if ($request->header('X-Inertia')) {
            $response->headers->set('Cache-Control', 'no-cache, must-revalidate');
            $response->headers->set('Surrogate-Control', 'no-store');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');

            return $response;
        }

        $response->headers->set('Cache-Control', 'no-cache, must-revalidate');
        $response->headers->set('Surrogate-Control', 'no-store');

        return $response;
    }
}