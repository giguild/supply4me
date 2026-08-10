<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        foreach ($permissions as $permission) {
            if (Gate::denies($permission)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Unauthorized.',
                        'error' => "You do not have the {$permission} permission.",
                    ], 403);
                }

                abort(403, "Unauthorized. You do not have the {$permission} permission.");
            }
        }

        return $next($request);
    }
}
