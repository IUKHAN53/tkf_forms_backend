<?php

namespace App\Http\Middleware;

use App\Services\LogActivity;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActivityLogger
{
    /**
     * Log admin interactions for observability.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Avoid noisy logs for assets/health checks
        if ($this->shouldSkip($request)) {
            return $response;
        }

        $userId = optional($request->user())->id;
        $path = $request->path();
        $method = $request->method();

        LogActivity::record(
            'http.request',
            "{$method} {$path}",
            [
                'query' => $request->query(),
            ],
            $userId,
            $request->ip(),
            substr((string) $request->userAgent(), 0, 255)
        );

        return $response;
    }

    private function shouldSkip(Request $request): bool
    {
        $path = $request->path();

        return $request->is('build/*')
            || $request->is('storage/*')
            || $request->is('images/*')
            || $request->is('favicon.ico')
            || $request->is('sanctum/csrf-cookie')
            || $request->is('telescope*');
    }
}
