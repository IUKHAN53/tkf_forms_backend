<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiRequestLogger
{
    /**
     * Log API requests and responses for debugging.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $requestId = uniqid('req_');

        // Log incoming request
        Log::channel('mobile')->info("API Request [{$requestId}]", [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'user_id' => optional($request->user())->id,
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 100),
        ]);

        // Process the request
        $response = $next($request);

        $duration = round((microtime(true) - $startTime) * 1000, 2);
        $statusCode = $response->getStatusCode();

        // Log response details
        $logData = [
            'status' => $statusCode,
            'duration_ms' => $duration,
        ];

        // Log validation errors for 422 responses
        if ($statusCode === 422) {
            $content = $response->getContent();
            $decoded = json_decode($content, true);
            $logData['validation_errors'] = $decoded['errors'] ?? null;
            $logData['message'] = $decoded['message'] ?? null;

            Log::channel('mobile')->warning("API Validation Failed [{$requestId}]", array_merge($logData, [
                'request_data' => $this->sanitizeRequestData($request->all()),
            ]));
        } elseif ($statusCode >= 400) {
            $content = $response->getContent();
            $decoded = json_decode($content, true);
            $logData['error'] = $decoded['message'] ?? substr($content, 0, 500);

            Log::channel('mobile')->error("API Error [{$requestId}]", $logData);
        } else {
            Log::channel('mobile')->info("API Response [{$requestId}]", $logData);
        }

        return $response;
    }

    /**
     * Sanitize request data to remove sensitive information.
     */
    private function sanitizeRequestData(array $data): array
    {
        $sensitiveKeys = ['password', 'password_confirmation', 'token', 'secret', 'api_key'];

        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), $sensitiveKeys)) {
                $data[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $data[$key] = $this->sanitizeRequestData($value);
            }
        }

        return $data;
    }
}
