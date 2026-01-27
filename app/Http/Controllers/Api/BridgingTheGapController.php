<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BridgingTheGap;
use App\Models\FgdsCommunity;
use App\Models\FgdsHealthWorkers;
use App\Models\Participant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BridgingTheGapController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $records = BridgingTheGap::with(['participants', 'teamMembers.participant'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($records);
    }

    public function store(Request $request): JsonResponse
    {
        // Log incoming request for debugging
        \Illuminate\Support\Facades\Log::info('Bridging The Gap store request', [
            'has_team_members' => $request->has('team_members'),
            'team_members_raw' => $request->input('team_members'),
            'all_keys' => array_keys($request->all()),
        ]);

        $validated = $request->validate([
            'date' => 'required|string',
            'venue' => 'required|string',
            'district' => 'required|string',
            'uc' => 'required|string',
            'fix_site' => 'required|string',
            'participants_males' => 'required|integer|min:0',
            'participants_females' => 'required|integer|min:0',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'device_info' => 'nullable|array',
            'started_at' => 'nullable|date',
            'submitted_at' => 'nullable|date',
            'unique_id' => 'nullable|string',

            // Attendance tab participants
            'participants' => 'required|array|min:1',
            'participants.*.name' => 'required|string',
            'participants.*.occupation' => 'required|string',
            'participants.*.contact_no' => 'required|string|regex:/^03\d{9}$/',

            // IIT Team members (participant IDs from other forms)
            // Relaxed validation - we verify participant exists when creating
            'team_members' => 'nullable|array',
            'team_members.*.participant_id' => 'required|integer',
            'team_members.*.source_type' => 'required|string|in:fgds_community,fgds_health_workers',
            'team_members.*.source_id' => 'required|integer',
        ]);

        $record = DB::transaction(function () use ($validated, $request) {
            $participantsData = $validated['participants'];
            $teamMembersData = $validated['team_members'] ?? [];
            unset($validated['participants'], $validated['team_members']);

            $validated['user_id'] = $request->user()->id;
            $validated['ip_address'] = $request->ip();
            $validated['submitted_at'] = $validated['submitted_at'] ?? now();

            $record = BridgingTheGap::create($validated);

            // Create attendance participants
            foreach ($participantsData as $index => $participant) {
                $record->participants()->create([
                    'sr_no' => $index + 1,
                    'name' => $participant['name'],
                    'occupation' => $participant['occupation'],
                    'contact_no' => $participant['contact_no'],
                ]);
            }

            // Link IIT team members - verify participant exists before creating
            \Illuminate\Support\Facades\Log::info('Processing IIT team members', [
                'bridging_the_gap_id' => $record->id,
                'team_members_count' => count($teamMembersData),
                'team_members_data' => $teamMembersData,
            ]);

            foreach ($teamMembersData as $index => $teamMember) {
                try {
                    $participantExists = Participant::where('id', $teamMember['participant_id'])->exists();

                    if ($participantExists) {
                        $created = $record->teamMembers()->create([
                            'participant_id' => $teamMember['participant_id'],
                            'source_type' => $teamMember['source_type'],
                            'source_id' => $teamMember['source_id'],
                        ]);

                        \Illuminate\Support\Facades\Log::info('IIT Team Member created', [
                            'index' => $index,
                            'team_member_id' => $created->id,
                            'participant_id' => $teamMember['participant_id'],
                        ]);
                    } else {
                        \Illuminate\Support\Facades\Log::warning('IIT Team Member participant not found', [
                            'bridging_the_gap_id' => $record->id,
                            'participant_id' => $teamMember['participant_id'],
                            'source_type' => $teamMember['source_type'],
                            'source_id' => $teamMember['source_id'],
                        ]);
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to create IIT Team Member', [
                        'bridging_the_gap_id' => $record->id,
                        'participant_id' => $teamMember['participant_id'],
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return $record;
        });

        $record->load(['participants', 'teamMembers.participant']);

        return response()->json([
            'message' => 'Bridging The Gap record created successfully.',
            'data' => $record,
            'team_members_saved' => $record->teamMembers->count(),
        ], 201);
    }

    public function show(BridgingTheGap $bridgingTheGap): JsonResponse
    {
        $bridgingTheGap->load(['participants', 'teamMembers.participant']);
        return response()->json($bridgingTheGap);
    }

    /**
     * Search participants from Community Barriers and Healthcare Barriers
     * filtered by UC for IIT team selection
     */
    public function searchParticipants(Request $request): JsonResponse
    {
        $request->validate([
            'uc' => 'required|string',
            'search' => 'nullable|string|min:2',
        ]);

        $uc = $request->uc;
        $search = $request->search;

        // Get UC variants to handle different spellings/formats
        $ucVariants = \App\Http\Controllers\Admin\DashboardController::getUcVariants(
            \App\Http\Controllers\Admin\DashboardController::getConsolidatedUcName($uc)
        );

        // Get participants from FGDs-Community in the same UC (or its variants)
        $fgdsCommunityIds = FgdsCommunity::where(function ($q) use ($ucVariants) {
                $q->whereIn('uc', $ucVariants);
                foreach ($ucVariants as $variant) {
                    $q->orWhere('uc', 'LIKE', $variant);
                }
            })->pluck('id');

        $communityParticipants = Participant::where('participantable_type', FgdsCommunity::class)
            ->whereIn('participantable_id', $fgdsCommunityIds)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('contact_no', 'like', "%{$search}%");
                });
            })
            ->get()
            ->map(function ($participant) {
                return [
                    'id' => $participant->id,
                    'name' => $participant->name,
                    'contact_no' => $participant->contact_no,
                    'occupation' => $participant->occupation,
                    'source_type' => 'fgds_community',
                    'source_id' => $participant->participantable_id,
                    'source_label' => 'FGDs-Community',
                ];
            });

        // Get participants from FGDs-Health Workers in the same UC (or its variants)
        $fgdsHealthWorkersIds = FgdsHealthWorkers::where(function ($q) use ($ucVariants) {
                $q->whereIn('uc', $ucVariants);
                foreach ($ucVariants as $variant) {
                    $q->orWhere('uc', 'LIKE', $variant);
                }
            })->pluck('id');

        $healthcareParticipants = Participant::where('participantable_type', FgdsHealthWorkers::class)
            ->whereIn('participantable_id', $fgdsHealthWorkersIds)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('contact_no', 'like', "%{$search}%");
                });
            })
            ->get()
            ->map(function ($participant) {
                return [
                    'id' => $participant->id,
                    'name' => $participant->name,
                    'contact_no' => $participant->contact_no,
                    'designation' => $participant->designation,
                    'source_type' => 'fgds_health_workers',
                    'source_id' => $participant->participantable_id,
                    'source_label' => 'FGDs-Health Workers',
                ];
            });

        // Combine and return results
        $allParticipants = $communityParticipants->merge($healthcareParticipants);

        return response()->json([
            'data' => $allParticipants->values(),
            'total' => $allParticipants->count(),
        ]);
    }
}
