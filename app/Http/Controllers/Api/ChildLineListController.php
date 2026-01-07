<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChildLineList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChildLineListController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $lists = ChildLineList::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($lists);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'division' => 'required|string',
            'district' => 'required|string',
            'town' => 'required|string',
            'uc' => 'required|string',
            'outreach' => 'required|string',
            'child_name' => 'required|string',
            'father_name' => 'required|string',
            'gender' => 'required|string|in:Male,Female',
            'date_of_birth' => 'required|date',
            'age_in_months' => 'required|integer',
            'father_cnic' => 'nullable|string',
            'house_number' => 'nullable|string',
            'address' => 'required|string',
            'guardian_phone' => 'nullable|string',
            'type' => 'required|string|in:Zero Dose,Zero Dose (ZD),Defaulter,Refusal',
            'missed_vaccines' => 'required|array',
            'missed_vaccines.*' => 'string',
            'reasons_of_missing' => 'required|string',
            'plan_for_coverage' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'device_info' => 'nullable|array',
            'started_at' => 'nullable|date',
            'submitted_at' => 'nullable|date',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['ip_address'] = $request->ip();
        $validated['submitted_at'] = $validated['submitted_at'] ?? now();

        $list = ChildLineList::create($validated);

        return response()->json([
            'message' => 'Child line list entry created successfully',
            'data' => $list,
        ], 201);
    }

    public function show(ChildLineList $childLineList): JsonResponse
    {
        return response()->json($childLineList);
    }
}
