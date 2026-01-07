<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormIdController extends Controller
{
    /**
     * Generate a unique form ID for a specific form type.
     * This allows the mobile app to display the ID before submission.
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'form_type' => 'required|in:fgds_community,fgds_health_workers,child_line_list,bridging_the_gap',
        ]);

        $prefixes = [
            'fgds_community' => 'FC',
            'fgds_health_workers' => 'FH',
            'child_line_list' => 'CL',
            'bridging_the_gap' => 'BG',
        ];

        $prefix = $prefixes[$request->form_type];
        $uuid = strtoupper(Str::random(8));
        $uniqueId = "{$prefix}-{$uuid}";

        return response()->json([
            'unique_id' => $uniqueId,
            'form_type' => $request->form_type,
        ]);
    }
}
