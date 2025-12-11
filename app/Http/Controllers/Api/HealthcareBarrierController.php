<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HealthcareBarrier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HealthcareBarrierController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $records = HealthcareBarrier::with('participants')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($records);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'hfs' => 'required|string',
            'address' => 'required|string',
            'uc' => 'required|string',
            'participants_males' => 'required|integer',
            'participants_females' => 'required|integer',
            'group_type' => 'required|string|in:Medics,Non-Medics,Both',
            'facilitator_tkf' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'participants' => 'required|array|min:1',
            'participants.*.name' => 'required|string',
            'participants.*.title_designation' => 'nullable|string',
            'participants.*.contact_no' => 'nullable|string',
            'participants.*.cnic' => 'nullable|string',
            'participants.*.gender' => 'nullable|string|in:Male,Female',
        ]);

        $record = DB::transaction(function () use ($validated, $request) {
            $participantsData = $validated['participants'];
            unset($validated['participants']);
            $validated['user_id'] = $request->user()->id;

            $record = HealthcareBarrier::create($validated);

            foreach ($participantsData as $index => $participant) {
                $record->participants()->create([
                    'sr_no' => $index + 1,
                    'name' => $participant['name'],
                    'title_designation' => $participant['title_designation'] ?? null,
                    'contact_no' => $participant['contact_no'] ?? null,
                    'cnic' => $participant['cnic'] ?? null,
                    'gender' => $participant['gender'] ?? null,
                ]);
            }

            return $record->load('participants');
        });

        return response()->json([
            'message' => 'Healthcare barriers record created successfully',
            'data' => $record,
        ], 201);
    }

    public function show(HealthcareBarrier $healthcareBarrier): JsonResponse
    {
        return response()->json($healthcareBarrier->load('participants'));
    }
}
