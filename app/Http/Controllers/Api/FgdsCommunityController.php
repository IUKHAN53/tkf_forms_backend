<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FgdsCommunity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FgdsCommunityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $records = FgdsCommunity::with('participants')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($records);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|string',
            'venue' => 'required|string',
            'uc' => 'required|string',
            'district' => 'required|string',
            'fix_site' => 'required|string',
            'outreach' => 'required|string',
            'community' => 'required|array|min:1',
            'community.*' => 'required|string',
            'participants_males' => 'required|integer',
            'participants_females' => 'required|integer',
            'facilitator_tkf' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'device_info' => 'nullable|array',
            'started_at' => 'nullable|date',
            'submitted_at' => 'nullable|date',
            'unique_id' => 'nullable|string',
            'participants' => 'required|array|min:1',
            'participants.*.name' => 'required|string',
            'participants.*.occupation' => 'nullable|string',
            'participants.*.address' => 'nullable|string',
            'participants.*.contact_no' => ['nullable', 'string', 'regex:/^03\d{9}$/'],
            'participants.*.cnic' => ['nullable', 'string', 'regex:/^\d{5}-\d{7}-\d$/'],
            'participants.*.gender' => 'nullable|string|in:Male,Female',
        ]);

        // Validate participant counts match actual participant data
        $participantsData = $validated['participants'];
        $actualMales = collect($participantsData)->where('gender', 'Male')->count();
        $actualFemales = collect($participantsData)->where('gender', 'Female')->count();
        $errors = [];
        if ($validated['participants_males'] != $actualMales) {
            $errors['participants_males'] = ["Males count ({$validated['participants_males']}) does not match actual male participants ({$actualMales})."];
        }
        if ($validated['participants_females'] != $actualFemales) {
            $errors['participants_females'] = ["Females count ({$validated['participants_females']}) does not match actual female participants ({$actualFemales})."];
        }
        if (!empty($errors)) {
            return response()->json([
                'message' => 'Participant count does not match the participant data entered.',
                'errors' => $errors,
            ], 422);
        }

        $record = DB::transaction(function () use ($validated, $request, $participantsData) {
            unset($validated['participants']);
            $validated['user_id'] = $request->user()->id;
            $validated['ip_address'] = $request->ip();
            $validated['submitted_at'] = $validated['submitted_at'] ?? now();

            $record = FgdsCommunity::create($validated);

            foreach ($participantsData as $index => $participant) {
                $record->participants()->create([
                    'sr_no' => $index + 1,
                    'name' => $participant['name'],
                    'occupation' => $participant['occupation'] ?? null,
                    'address' => $participant['address'] ?? null,
                    'contact_no' => $participant['contact_no'] ?? null,
                    'cnic' => $participant['cnic'] ?? null,
                    'gender' => $participant['gender'] ?? null,
                ]);
            }

            return $record->load('participants');
        });

        return response()->json([
            'message' => 'FGDs-Community record created successfully',
            'data' => $record,
        ], 201);
    }

    public function show(FgdsCommunity $fgdsCommunity): JsonResponse
    {
        return response()->json($fgdsCommunity->load('participants'));
    }
}
