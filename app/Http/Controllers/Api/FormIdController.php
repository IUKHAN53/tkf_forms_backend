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
            'form_type' => 'required|in:area_mapping,draft_list,religious_leader,community_barrier,healthcare_barrier,bridging_the_gap',
        ]);

        $prefixes = [
            'area_mapping' => 'AM',
            'draft_list' => 'DL',
            'religious_leader' => 'RL',
            'community_barrier' => 'CB',
            'healthcare_barrier' => 'HB',
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
