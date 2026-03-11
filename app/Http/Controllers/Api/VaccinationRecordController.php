<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VaccinationRecord;
use App\Services\LogActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VaccinationRecordController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = VaccinationRecord::where('user_id', $request->user()->id)->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('vaccinated')) {
            $query->where('vaccinated', $request->vaccinated);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('child_name', 'like', "%{$search}%")
                    ->orWhere('father_name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $records = $query->paginate($request->input('per_page', 50));

        return response()->json($records);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fix_site' => 'nullable|string|max:255',
            'uc' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'serial_number' => 'nullable|integer|min:0',
            'child_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'age' => 'required|string|max:100',
            'address' => 'nullable|string|max:500',
            'contact_number' => 'nullable|string|max:50',
            'category' => 'required|string|in:Defaulter,Refusal,Zero Dose',
            'vaccinated' => 'required|string|in:YES,NO',
            'date_of_vaccination' => 'nullable|date',
            'community_member_name' => 'nullable|string|max:255',
            'community_member_contact' => 'nullable|string|max:50',
            'gps_coordinates' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'device_info' => 'nullable|array',
            'started_at' => 'nullable|date',
            'submitted_at' => 'nullable|date',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['ip_address'] = $request->ip();
        $validated['submitted_at'] = $validated['submitted_at'] ?? now();

        $record = VaccinationRecord::create($validated);

        LogActivity::record(
            'vaccination_record.created',
            "Created vaccination record for {$validated['child_name']}",
            ['record_id' => $record->id, 'category' => $validated['category'], 'child_name' => $validated['child_name']],
            $request->user()->id,
            $request->ip()
        );

        return response()->json([
            'message' => 'Vaccination record created successfully',
            'data' => $record,
        ], 201);
    }

    public function show(VaccinationRecord $vaccinationRecord): JsonResponse
    {
        return response()->json($vaccinationRecord);
    }

    public function update(Request $request, VaccinationRecord $vaccinationRecord): JsonResponse
    {
        $validated = $request->validate([
            'fix_site' => 'nullable|string|max:255',
            'uc' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'serial_number' => 'nullable|integer|min:0',
            'child_name' => 'sometimes|required|string|max:255',
            'father_name' => 'sometimes|required|string|max:255',
            'age' => 'sometimes|required|string|max:100',
            'address' => 'nullable|string|max:500',
            'contact_number' => 'nullable|string|max:50',
            'category' => 'sometimes|required|string|in:Defaulter,Refusal,Zero Dose',
            'vaccinated' => 'sometimes|required|string|in:YES,NO',
            'date_of_vaccination' => 'nullable|date',
            'community_member_name' => 'nullable|string|max:255',
            'community_member_contact' => 'nullable|string|max:50',
            'gps_coordinates' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $vaccinationRecord->update($validated);

        LogActivity::record(
            'vaccination_record.updated',
            "Updated vaccination record #{$vaccinationRecord->id} ({$vaccinationRecord->child_name})",
            ['record_id' => $vaccinationRecord->id, 'changed_fields' => array_keys($validated)],
            $request->user()->id,
            $request->ip()
        );

        return response()->json([
            'message' => 'Vaccination record updated successfully',
            'data' => $vaccinationRecord->fresh(),
        ]);
    }

    public function destroy(Request $request, VaccinationRecord $vaccinationRecord): JsonResponse
    {
        LogActivity::record(
            'vaccination_record.deleted',
            "Deleted vaccination record #{$vaccinationRecord->id} ({$vaccinationRecord->child_name})",
            ['record_id' => $vaccinationRecord->id, 'child_name' => $vaccinationRecord->child_name, 'father_name' => $vaccinationRecord->father_name],
            $request->user()->id,
            $request->ip()
        );

        $vaccinationRecord->delete();

        return response()->json([
            'message' => 'Vaccination record deleted successfully',
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $query = VaccinationRecord::where('user_id', $request->user()->id);

        return response()->json([
            'total_defaulters' => (clone $query)->count(),
            'vaccinated' => (clone $query)->where('vaccinated', 'YES')->count(),
            'pending_refusals' => (clone $query)->where('category', 'Refusal')->where('vaccinated', 'NO')->count(),
            'zero_dose_cases' => (clone $query)->where('category', 'Zero Dose')->count(),
        ]);
    }
}
