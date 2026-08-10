<?php

namespace App\Http\Middleware;

use App\Models\Core\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->company) {
            if ($user->company->status !== 'active') {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Your company account has been deactivated. Please contact support.');
            }
        }

        return $next($request);
    }
}
