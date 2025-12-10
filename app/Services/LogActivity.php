<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogActivity
{
    /**
     * Persist a structured activity log entry.
     */
    public static function record(
        string $action,
        string $description,
        array $context = [],
        ?int $userId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): void {
        try {
            ActivityLog::create([
                'user_id' => $userId ?? Auth::id(),
                'action' => $action,
                'description' => $description,
                'ip_address' => $ipAddress ?? request()->ip(),
                'user_agent' => $userAgent ?? request()->userAgent(),
                'context' => $context,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to write activity log', [
                'error' => $e->getMessage(),
                'action' => $action,
            ]);
        }
    }
}
