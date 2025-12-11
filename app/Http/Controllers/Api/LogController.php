<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{
    /**
     * Store logs from mobile app.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'app_version' => 'nullable|string|max:50',
            'build_number' => 'nullable|string|max:50',
            'platform' => 'nullable|string|max:50',
            'user_id' => 'nullable|integer',
            'timestamp' => 'nullable|string',
            'queue_count' => 'nullable|integer',
            'sync_status' => 'nullable|string|max:50',
            'last_sync_at' => 'nullable|string',
        ]);

        // Get user from auth if available
        $user = $request->user();

        // Log the diagnostic data
        Log::channel('mobile')->info('Mobile App Diagnostic Log', [
            'user_id' => $user?->id ?? $request->input('user_id'),
            'user_email' => $user?->email,
            'app_version' => $request->input('app_version'),
            'build_number' => $request->input('build_number'),
            'platform' => $request->input('platform'),
            'timestamp' => $request->input('timestamp'),
            'queue_count' => $request->input('queue_count'),
            'sync_status' => $request->input('sync_status'),
            'last_sync_at' => $request->input('last_sync_at'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'message' => 'Logs received successfully',
        ]);
    }
}
