<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentBranch
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $branchId = $request->header('X-Current-Branch') ?? Session::get('current_branch_id');

            if ($branchId) {
                $branch = $user->branches()->where('branches.id', $branchId)->first();

                if ($branch) {
                    Session::put('current_branch_id', $branch->id);
                    $request->merge(['current_branch_id' => $branch->id]);
                }
            } else {
                $defaultBranch = $user->branches()->where('is_main', true)->first()
                    ?? $user->branches()->first();

                if ($defaultBranch) {
                    Session::put('current_branch_id', $defaultBranch->id);
                    $request->merge(['current_branch_id' => $defaultBranch->id]);
                }
            }
        }

        return $next($request);
    }
}
