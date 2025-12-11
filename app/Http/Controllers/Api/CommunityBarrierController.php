<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityBarrier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunityBarrierController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $records = CommunityBarrier::with('participants')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($records);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'venue' => 'required|string',
            'uc' => 'required|string',
            'district' => 'required|string',
            'fix_site' => 'required|string',
            'outreach' => 'required|string',
            'community' => 'required|string|in:Pathan,Punjabi,Sindhi,Saraiki,Urdu speaking',
            'group_type' => 'required|string|in:Teachers,Shopkeepers,Religious Leaders,Political Leaders,Mother in Laws,Mothers,Fathers,Father in Laws',
            'participants_males' => 'required|integer',
            'participants_females' => 'required|integer',
            'facilitator_tkf' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'participants' => 'required|array|min:1',
            'participants.*.name' => 'required|string',
            'participants.*.occupation' => 'nullable|string',
            'participants.*.address' => 'nullable|string',
            'participants.*.contact_no' => 'nullable|string',
            'participants.*.cnic' => 'nullable|string',
            'participants.*.gender' => 'nullable|string|in:Male,Female',
        ]);

        $record = DB::transaction(function () use ($validated, $request) {
            $participantsData = $validated['participants'];
            unset($validated['participants']);
            $validated['user_id'] = $request->user()->id;

            $record = CommunityBarrier::create($validated);

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
            'message' => 'Community barriers record created successfully',
            'data' => $record,
        ], 201);
    }

    public function show(CommunityBarrier $communityBarrier): JsonResponse
    {
        return response()->json($communityBarrier->load('participants'));
    }
}
