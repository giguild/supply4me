<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Facades\Activity;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user() && $this->shouldLog($request)) {
            $this->logActivity($request, $response);
        }

        return $response;
    }

    protected function shouldLog(Request $request): bool
    {
        $method = $request->method();
        $path = $request->path();

        $excludedPaths = [
            'api/v1/auth/login',
            'api/v1/auth/register',
            'favicon.ico',
        ];

        foreach ($excludedPaths as $excluded) {
            if (str_starts_with($path, $excluded)) {
                return false;
            }
        }

        return in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE']);
    }

    protected function logActivity(Request $request, Response $response): void
    {
        try {
            $user = $request->user();
            $method = $request->method();
            $path = $request->path();
            $statusCode = $response->getStatusCode();

            $properties = [
                'method' => $method,
                'path' => $path,
                'status_code' => $statusCode,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ];

            if (in_array($method, ['POST', 'PUT', 'PATCH']) && $request->has(['password', 'password_confirmation'])) {
                $properties['request_data'] = collect($request->except(['password', 'password_confirmation']))
                    ->only(['name', 'email'])
                    ->toArray();
            } else {
                $properties['request_data'] = $request->except(['password', 'password_confirmation', '_token']);
            }

            Activity::performedOn($user)
                ->withProperties($properties)
                ->event($this->getEventName($method, $statusCode))
                ->causedBy($user)
                ->log("User {$method} {$path}");
        } catch (\Exception $e) {
            Log::error('Failed to log activity', [
                'error' => $e->getMessage(),
                'path' => $request->path(),
                'method' => $request->method(),
            ]);
        }
    }

    protected function getEventName(string $method, int $statusCode): string
    {
        $events = [
            'POST' => $statusCode >= 200 && $statusCode < 300 ? 'created' : 'failed_create',
            'PUT' => $statusCode >= 200 && $statusCode < 300 ? 'updated' : 'failed_update',
            'PATCH' => $statusCode >= 200 && $statusCode < 300 ? 'updated' : 'failed_update',
            'DELETE' => $statusCode >= 200 && $statusCode < 300 ? 'deleted' : 'failed_delete',
        ];

        return $events[$method] ?? 'unknown';
    }
}
