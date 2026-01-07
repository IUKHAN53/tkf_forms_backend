<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FgdsHealthWorkers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FgdsHealthWorkersController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $records = FgdsHealthWorkers::with('participants')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($records);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|string',
            'hfs' => 'required|string',
            'address' => 'required|string',
            'uc' => 'required|string',
            'participants_males' => 'required|integer',
            'participants_females' => 'required|integer',
            'group_type' => 'required|string|in:Medics,Non-Medics,Both',
            'facilitator_tkf' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'device_info' => 'nullable|array',
            'started_at' => 'nullable|date',
            'submitted_at' => 'nullable|date',
            'participants' => 'required|array|min:1',
            'participants.*.name' => 'required|string',
            'participants.*.designation' => 'nullable|string',
            'participants.*.contact_no' => 'nullable|string|regex:/^03\d{9}$/',
            'participants.*.cnic' => 'nullable|string|regex:/^\d{5}-\d{7}-\d$/',
            'participants.*.gender' => 'nullable|string|in:Male,Female',
        ]);

        $record = DB::transaction(function () use ($validated, $request) {
            $participantsData = $validated['participants'];
            unset($validated['participants']);
            $validated['user_id'] = $request->user()->id;
            $validated['ip_address'] = $request->ip();
            $validated['submitted_at'] = $validated['submitted_at'] ?? now();

            $record = FgdsHealthWorkers::create($validated);

            foreach ($participantsData as $index => $participant) {
                $record->participants()->create([
                    'sr_no' => $index + 1,
                    'name' => $participant['name'],
                    'designation' => $participant['designation'] ?? null,
                    'contact_no' => $participant['contact_no'] ?? null,
                    'cnic' => $participant['cnic'] ?? null,
                    'gender' => $participant['gender'] ?? null,
                ]);
            }

            return $record->load('participants');
        });

        return response()->json([
            'message' => 'FGDs-Health Workers record created successfully',
            'data' => $record,
        ], 201);
    }

    public function show(FgdsHealthWorkers $fgdsHealthWorker): JsonResponse
    {
        return response()->json($fgdsHealthWorker->load('participants'));
    }
}
