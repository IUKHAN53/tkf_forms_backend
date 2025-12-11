<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AreaMapping;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AreaMappingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $mappings = AreaMapping::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($mappings);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'district' => 'required|string',
            'town' => 'required|string',
            'uc_name' => 'required|string',
            'fix_site' => 'required|string',
            'outreach_name' => 'required|string',
            'outreach_coordinates' => 'nullable|string',
            'area_name' => 'required|string',
            'assigned_aic' => 'required|string',
            'aic_contact' => 'nullable|string',
            'assigned_cm' => 'required|string',
            'cm_contact' => 'nullable|string',
            'total_population' => 'required|integer',
            'total_under_2_years' => 'required|integer',
            'total_zero_dose' => 'required|integer',
            'total_defaulter' => 'required|integer',
            'total_refusal' => 'required|integer',
            'total_boys_under_2' => 'nullable|integer',
            'total_girls_under_2' => 'nullable|integer',
            'major_ethnicity' => 'nullable|string',
            'major_languages' => 'nullable|string',
            'existing_committees' => 'nullable|string',
            'nearest_phf' => 'nullable|string',
            'hf_incharge_name' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $validated['user_id'] = $request->user()->id;

        $mapping = AreaMapping::create($validated);

        return response()->json([
            'message' => 'Area mapping created successfully',
            'data' => $mapping,
        ], 201);
    }

    public function show(AreaMapping $areaMapping): JsonResponse
    {
        return response()->json($areaMapping);
    }
}
