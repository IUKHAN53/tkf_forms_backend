<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BridgingTheGap;
use App\Models\BridgingTheGapActionPlan;
use App\Models\BridgingTheGapActionPlanPhoto;
use App\Models\BridgingTheGapTeamMember;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BridgingTheGapController extends Controller
{
    public function index(Request $request)
    {
        // Use withCount instead of eager loading participant relationship to avoid issues with deleted participants
        $query = BridgingTheGap::with(['participants', 'teamMembers', 'user'])
            ->withCount([
                'actionPlans',
                'actionPlans as completed_action_plans_count' => fn ($q) => $q->where('implementation_status', 'completed'),
            ])
            ->latest();

        // Text search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('district', 'like', "%{$search}%")
                    ->orWhere('uc', 'like', "%{$search}%")
                    ->orWhere('venue', 'like', "%{$search}%");
            });
        }

        // District filter
        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        // UC filter
        if ($request->filled('uc')) {
            $query->where('uc', $request->uc);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Venue filter
        if ($request->filled('venue')) {
            $query->where('venue', 'like', "%{$request->venue}%");
        }

        $perPage = $request->input('per_page', 15);
        $records = $query->paginate($perPage == 'all' ? 999999 : (int) $perPage)->withQueryString();

        // Get distinct values for filter dropdowns
        $districts = BridgingTheGap::distinct()->pluck('district')->filter()->sort()->values();
        $ucs = BridgingTheGap::distinct()->pluck('uc')->filter()->sort()->values();

        // Calculate statistics
        $stats = [
            'total' => BridgingTheGap::count(),
            'total_action_plans' => BridgingTheGapActionPlan::count(),
            'total_attendance' => BridgingTheGap::selectRaw('SUM(participants_males + participants_females) as total')->value('total') ?? 0,
            'total_males' => BridgingTheGap::sum('participants_males') ?? 0,
            'total_females' => BridgingTheGap::sum('participants_females') ?? 0,
            'total_iit_members' => \App\Models\BridgingTheGapTeamMember::count(),
        ];

        // Prepare map data
        $mapData = BridgingTheGap::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($item) {
                return [
                    'lat' => (float) $item->latitude,
                    'lon' => (float) $item->longitude,
                    'popup' => "<strong>{$item->date}</strong><br>
                                District: {$item->district}<br>
                                UC: {$item->uc}<br>
                                Venue: {$item->venue}<br>
                                Participants: " . ($item->participants_males + $item->participants_females)
                ];
            })
            ->values()
            ->toArray();

        return view('admin.core-forms.bridging-the-gap.index', compact('records', 'mapData', 'districts', 'ucs', 'stats'));
    }

    public function show(BridgingTheGap $bridgingTheGap)
    {
        $bridgingTheGap->load([
            'participants' => fn ($q) => $q->orderBy('sr_no')->orderBy('id'),
            'teamMembers.participant',
            'user',
            'actionPlans.photos',
        ]);
        return view('admin.core-forms.bridging-the-gap.show', compact('bridgingTheGap'));
    }

    public function edit(BridgingTheGap $bridgingTheGap)
    {
        $bridgingTheGap->load(['participants', 'teamMembers.participant', 'user', 'actionPlans']);
        return view('admin.core-forms.bridging-the-gap.edit', compact('bridgingTheGap'));
    }

    public function update(Request $request, BridgingTheGap $bridgingTheGap)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'district' => 'required|string|max:255',
            'uc' => 'required|string|max:255',
            'fix_site' => 'nullable|string|max:255',
            'venue' => 'required|string|max:255',
            'participants_males' => 'required|integer|min:0',
            'participants_females' => 'required|integer|min:0',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $bridgingTheGap->update($validated);

        return redirect()->route('admin.bridging-the-gap.show', $bridgingTheGap)
            ->with('success', 'Bridging The Gap record updated successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $records = BridgingTheGap::whereIn('id', $request->ids)->get();
        $deleted = 0;

        foreach ($records as $record) {
            $record->teamMembers()->delete();
            $record->participants()->delete();
            BridgingTheGapActionPlan::purge($record->actionPlans());
            $record->delete();
            $deleted++;
        }

        return redirect()->route('admin.bridging-the-gap.index')
            ->with('success', "{$deleted} record(s) deleted successfully.");
    }

    public function toggleIitMember(BridgingTheGap $bridgingTheGap, Participant $participant)
    {
        if (! $bridgingTheGap->ownsParticipant($participant)) {
            return back()->with('error', 'Participant does not belong to this session.');
        }

        $existing = BridgingTheGapTeamMember::where('bridging_the_gap_id', $bridgingTheGap->id)
            ->where('participant_id', $participant->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', "{$participant->name} removed from IIT team members.");
        }

        BridgingTheGapTeamMember::create([
            'bridging_the_gap_id' => $bridgingTheGap->id,
            'participant_id' => $participant->id,
            'source_type' => 'bridging_the_gap',
            'source_id' => $bridgingTheGap->id,
        ]);

        return back()->with('success', "{$participant->name} marked as IIT team member.");
    }

    public function destroy(BridgingTheGap $bridgingTheGap)
    {
        // Delete team members first (references to external participants)
        $bridgingTheGap->teamMembers()->delete();

        // Delete attendance participants (morphMany relationship)
        $bridgingTheGap->participants()->delete();

        // Delete action plans (and their activity-picture files)
        BridgingTheGapActionPlan::purge($bridgingTheGap->actionPlans());

        // Delete the main record
        $bridgingTheGap->delete();

        return redirect()->route('admin.bridging-the-gap.index')
            ->with('success', 'Bridging The Gap record deleted successfully.');
    }

    public function export()
    {
        $records = BridgingTheGap::with(['participants', 'teamMembers', 'user'])->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bridging_the_gap_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['ID', 'Form ID', 'Date', 'Venue', 'District', 'UC', 'Fix Site', 'Males', 'Females', 'Total Attendance Participants', 'Total IIT Members', 'Submitted By', 'Latitude', 'Longitude', 'Created At'];

        $callback = function () use ($records, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($records as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->unique_id,
                    $item->date,
                    $item->venue,
                    $item->district,
                    $item->uc,
                    $item->fix_site,
                    $item->participants_males,
                    $item->participants_females,
                    $item->participants->count(),
                    $item->teamMembers->count(),
                    $item->user->name ?? 'N/A',
                    $item->latitude,
                    $item->longitude,
                    $item->created_at,
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function template()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bridging_the_gap_template.csv"',
        ];

        $columns = ['date', 'venue', 'district', 'uc', 'fix_site', 'participants_males', 'participants_females', 'latitude', 'longitude'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['2025-01-15 10:00:00', 'Community Center', 'District Name', 'UC Name', 'Fix Site Name', '10', '15', '31.5204', '74.3587']);
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        $header = fgetcsv($handle);
        $imported = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 9) continue;

            try {
                BridgingTheGap::create([
                    'date' => $row[0],
                    'venue' => $row[1],
                    'district' => $row[2],
                    'uc' => $row[3],
                    'fix_site' => $row[4],
                    'participants_males' => (int) $row[5],
                    'participants_females' => (int) $row[6],
                    'latitude' => $row[7] ?: null,
                    'longitude' => $row[8] ?: null,
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($imported + 2) . ": " . $e->getMessage();
            }
        }

        fclose($handle);

        $message = "Successfully imported {$imported} records.";
        if (count($errors) > 0) {
            $message .= " " . count($errors) . " errors occurred.";
        }

        return redirect()->route('admin.bridging-the-gap.index')
            ->with('success', $message);
    }

    public function uploadActionPlan(Request $request, $id)
    {
        $request->validate([
            'action_plan_file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        $record = BridgingTheGap::findOrFail($id);

        // Store the file
        $file = $request->file('action_plan_file');
        $filename = 'action_plan_' . $record->unique_id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('action_plans/bridging_the_gap', $filename, 'public');

        // Update the record with the file path
        $record->update([
            'action_plan_file' => $path,
        ]);

        // Parse the Excel file and extract action plans
        try {
            $importResult = $this->parseAndStoreActionPlans($record, $file->getRealPath());
            $message = "Action plan uploaded successfully for record {$record->unique_id}. ";
            $message .= "Imported {$importResult['imported']} action items.";
            if ($importResult['skipped'] > 0) {
                $message .= " Skipped {$importResult['skipped']} empty rows.";
            }
        } catch (\Exception $e) {
            $error = "File uploaded but failed to parse action plans: " . $e->getMessage();
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $error], 422);
            }
            return redirect()->route('admin.bridging-the-gap.index')->with('error', $error);
        }

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('admin.bridging-the-gap.index')
            ->with('success', $message);
    }

    /**
     * Parse Excel file and store action plans
     */
    private function parseAndStoreActionPlans(BridgingTheGap $record, string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        if (empty($rows)) {
            return ['imported' => 0, 'skipped' => 0];
        }

        // Resolve column positions from the header row so files with a leading
        // serial-number column, a missing "Sub Cause"/"Root Cause" column, an
        // older narrower layout, or reordered columns all import into the
        // correct fields.
        $map = $this->resolveActionPlanColumns($rows[0]);

        // Delete existing action plans (with their activity pictures) for this
        // record before importing new ones — an import replaces, it doesn't merge.
        BridgingTheGapActionPlan::purge(BridgingTheGapActionPlan::where('bridging_the_gap_id', $record->id));

        $imported = 0;
        $skipped = 0;
        $serialNumber = 1;

        // Skip header row (index 0), process data rows
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Skip header

            $get = fn ($key) => ($map[$key] !== null && isset($row[$map[$key]]))
                ? trim((string) $row[$map[$key]])
                : '';

            $problem = $get('problem');
            $subCause = $get('sub_cause');
            $rootCause = $get('root_cause');
            $solution = $get('solution');
            $actionNeeded = $get('action_needed');
            $whoIsResponsible = $get('who_is_responsible');
            $timeline = $get('timeline');

            // Skip empty problem rows (problem is required)
            if ($problem === '') {
                $skipped++;
                continue;
            }

            // Create the action plan record
            BridgingTheGapActionPlan::create([
                'bridging_the_gap_id' => $record->id,
                'problem' => $problem,
                'sub_cause' => $subCause ?: null,
                'root_cause' => $rootCause ?: null,
                'solution' => $solution ?: null,
                'action_needed' => $actionNeeded ?: null,
                'who_is_responsible' => $whoIsResponsible ?: null,
                'timeline' => $timeline ?: null,
                'serial_number' => $serialNumber++,
            ]);

            $imported++;
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
        ];
    }

    /**
     * Map action-plan fields to column indexes using the header row.
     *
     * Matching is by header name (case/spacing/punctuation insensitive), so a
     * leading "Sr. No" column is ignored, an absent "Sub Cause" or "Root Cause"
     * column stays null, and columns may appear in any order. Falls back to the
     * canonical positional layout (Problem | Sub Cause | Root Cause | Solution |
     * Action Needed | Responsible | Timeline) only when no headers are recognized.
     *
     * "Sub Cause" and "Root Cause" both normalize to a string containing
     * "cause", so they are matched on their full prefixed forms and the bare
     * "cause" fallback is reserved for root cause.
     */
    private function resolveActionPlanColumns(array $header): array
    {
        $map = [
            'problem' => null,
            'sub_cause' => null,
            'root_cause' => null,
            'solution' => null,
            'action_needed' => null,
            'who_is_responsible' => null,
            'timeline' => null,
        ];

        $recognized = false;
        foreach ($header as $i => $cell) {
            $h = preg_replace('/[^a-z0-9]/', '', strtolower((string) $cell));
            if ($h === '') continue;

            if ($map['problem'] === null && str_contains($h, 'problem')) {
                $map['problem'] = $i; $recognized = true;
            } elseif ($map['sub_cause'] === null && (str_contains($h, 'subcause') || str_contains($h, 'subsidiarycause') || str_contains($h, 'secondarycause'))) {
                $map['sub_cause'] = $i; $recognized = true;
            } elseif ($map['root_cause'] === null && (str_contains($h, 'rootcause') || $h === 'cause')) {
                $map['root_cause'] = $i; $recognized = true;
            } elseif ($map['solution'] === null && str_contains($h, 'solution')) {
                $map['solution'] = $i; $recognized = true;
            } elseif ($map['action_needed'] === null && str_contains($h, 'action')) {
                $map['action_needed'] = $i; $recognized = true;
            } elseif ($map['who_is_responsible'] === null && str_contains($h, 'responsible')) {
                $map['who_is_responsible'] = $i; $recognized = true;
            } elseif ($map['timeline'] === null && (str_contains($h, 'timeline') || str_contains($h, 'deadline') || str_contains($h, 'duration'))) {
                $map['timeline'] = $i; $recognized = true;
            }
        }

        // Fallback to the canonical positional layout if the header wasn't recognized.
        if (!$recognized || $map['problem'] === null) {
            $map = [
                'problem' => 0,
                'sub_cause' => 1,
                'root_cause' => 2,
                'solution' => 3,
                'action_needed' => 4,
                'who_is_responsible' => 5,
                'timeline' => 6,
            ];
        }

        return $map;
    }

    /**
     * Get action plans for a specific Bridging The Gap record (JSON)
     */
    public function getActionPlans($id)
    {
        $record = BridgingTheGap::findOrFail($id);
        $actionPlans = $record->actionPlans()->with('photos')->orderBy('serial_number')->get();

        return response()->json([
            'action_plans' => $actionPlans,
            'statuses' => BridgingTheGapActionPlan::STATUSES,
            'record' => [
                'id' => $record->id,
                'unique_id' => $record->unique_id,
            ],
        ]);
    }

    /**
     * Validation rules shared by store and update. On update every field is
     * optional (`sometimes`) so a status change can be saved on its own.
     */
    private function actionPlanRules(bool $partial = false): array
    {
        $required = $partial ? 'sometimes|required|string' : 'required|string';

        return [
            'problem' => $required,
            'sub_cause' => 'nullable|string',
            'root_cause' => 'nullable|string',
            'solution' => 'nullable|string',
            'action_needed' => 'nullable|string',
            'who_is_responsible' => 'nullable|string|max:255',
            'timeline' => 'nullable|string|max:255',
            'implementation_status' => ['sometimes', 'required', Rule::in(array_keys(BridgingTheGapActionPlan::STATUSES))],
            'implementation_remarks' => 'nullable|string|max:5000',
        ];
    }

    /**
     * Store a single action plan for a Bridging The Gap record
     */
    public function storeActionPlan(Request $request, $id)
    {
        $record = BridgingTheGap::findOrFail($id);

        $validated = $request->validate($this->actionPlanRules());

        $maxSerial = $record->actionPlans()->max('serial_number') ?? 0;
        $status = $validated['implementation_status'] ?? BridgingTheGapActionPlan::DEFAULT_STATUS;

        $actionPlan = BridgingTheGapActionPlan::create([
            'bridging_the_gap_id' => $record->id,
            'problem' => $validated['problem'],
            'sub_cause' => $validated['sub_cause'] ?? null,
            'root_cause' => $validated['root_cause'] ?? null,
            'solution' => $validated['solution'] ?? null,
            'action_needed' => $validated['action_needed'] ?? null,
            'who_is_responsible' => $validated['who_is_responsible'] ?? null,
            'timeline' => $validated['timeline'] ?? null,
            'implementation_status' => $status,
            'implementation_remarks' => $validated['implementation_remarks'] ?? null,
            'status_updated_at' => $status !== BridgingTheGapActionPlan::DEFAULT_STATUS ? now() : null,
            'serial_number' => $maxSerial + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Action plan added successfully.',
            'action_plan' => $actionPlan->load('photos'),
        ]);
    }

    /**
     * Update an individual action plan (full edit, or just its status/remarks)
     */
    public function updateActionPlan(Request $request, $actionPlanId)
    {
        $actionPlan = BridgingTheGapActionPlan::findOrFail($actionPlanId);

        $validated = $request->validate($this->actionPlanRules(partial: true));

        if (isset($validated['implementation_status'])
            && $validated['implementation_status'] !== $actionPlan->implementation_status) {
            $validated['status_updated_at'] = now();
        }

        $actionPlan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Action plan updated successfully.',
            'action_plan' => $actionPlan->fresh('photos'),
        ]);
    }

    /**
     * Delete an individual action plan
     */
    public function deleteActionPlan($actionPlanId)
    {
        $actionPlan = BridgingTheGapActionPlan::findOrFail($actionPlanId);
        BridgingTheGapActionPlan::purge(BridgingTheGapActionPlan::whereKey($actionPlan->id));

        return response()->json([
            'success' => true,
            'message' => 'Action plan deleted successfully.',
        ]);
    }

    /**
     * Delete all action plans for a specific Bridging The Gap record
     */
    public function deleteAllActionPlans($id)
    {
        $record = BridgingTheGap::findOrFail($id);
        $count = BridgingTheGapActionPlan::purge($record->actionPlans());

        return response()->json([
            'success' => true,
            'message' => "{$count} action plan(s) deleted successfully.",
        ]);
    }

    /* ===================================================================== *
     *  Action-plan activity pictures
     * ===================================================================== */

    /**
     * Upload a supporting activity picture for an action-plan item. The admin
     * page downscales images in the browser first, so this normally receives a
     * few hundred KB; the 2 MB cap matches the server's upload_max_filesize.
     */
    public function uploadActionPlanPhoto(Request $request, $actionPlanId)
    {
        $actionPlan = BridgingTheGapActionPlan::findOrFail($actionPlanId);

        $request->validate([
            'photo' => 'required|file|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'photo.max' => 'The picture must be smaller than 2 MB.',
            'photo.uploaded' => 'The picture could not be uploaded — it may be larger than the server allows (2 MB).',
        ]);

        $file = $request->file('photo');
        $path = $file->store('action_plan_photos/' . $actionPlan->bridging_the_gap_id, BridgingTheGapActionPlanPhoto::DISK);

        $photo = $actionPlan->photos()->create([
            'path' => $path,
            'original_name' => mb_substr($file->getClientOriginalName(), 0, 255),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => $request->user()?->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Picture uploaded.',
            'photo' => $photo,
        ]);
    }

    /** Stream an activity picture (auth-protected; files are not public). */
    public function showActionPlanPhoto(BridgingTheGapActionPlanPhoto $photo)
    {
        $disk = Storage::disk(BridgingTheGapActionPlanPhoto::DISK);
        abort_unless($disk->exists($photo->path), 404);

        return $disk->response($photo->path, $photo->original_name, [
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }

    public function deleteActionPlanPhoto(BridgingTheGapActionPlanPhoto $photo)
    {
        Storage::disk(BridgingTheGapActionPlanPhoto::DISK)->delete($photo->path);
        $photo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Picture deleted.',
        ]);
    }

    /* ===================================================================== *
     *  Attendance sheet (participants) — add / edit / delete after submission
     * ===================================================================== */

    public function storeParticipant(Request $request, BridgingTheGap $bridgingTheGap)
    {
        $validated = $request->validate(Participant::adminRules());

        $participant = $bridgingTheGap->addAttendee($validated);

        if ($request->boolean('mark_iit')) {
            BridgingTheGapTeamMember::firstOrCreate(
                ['bridging_the_gap_id' => $bridgingTheGap->id, 'participant_id' => $participant->id],
                ['source_type' => 'bridging_the_gap', 'source_id' => $bridgingTheGap->id]
            );
        }

        return back()->with('success', "{$participant->name} added to the attendance sheet.");
    }

    public function updateParticipant(Request $request, BridgingTheGap $bridgingTheGap, Participant $participant)
    {
        if (! $this->editableParticipant($bridgingTheGap, $participant)) {
            return back()->with('error', 'That person is not part of this session.');
        }

        $participant->update($request->validate(Participant::adminRules()));

        return back()->with('success', "{$participant->name}'s details were updated.");
    }

    public function destroyParticipant(BridgingTheGap $bridgingTheGap, Participant $participant)
    {
        if (! $bridgingTheGap->ownsParticipant($participant)) {
            return back()->with('error', 'Participant does not belong to this session.');
        }

        $name = $participant->name;
        $participant->deleteWithReferences();

        return back()->with('success', "{$name} removed from the attendance sheet.");
    }

    /**
     * A person can be edited from this session if they are on its attendance
     * sheet, or are one of its IIT members (a few were picked from FGDs).
     */
    private function editableParticipant(BridgingTheGap $bridgingTheGap, Participant $participant): bool
    {
        return $bridgingTheGap->ownsParticipant($participant)
            || $bridgingTheGap->teamMembers()->where('participant_id', $participant->id)->exists();
    }

    /**
     * Download sample action plan Excel template
     */
    public function actionPlanSample()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Canonical action-plan layout. Keep in sync with
        // resolveActionPlanColumns() and the action-plan tables in the
        // Bridging The Gap views.
        $headers = ['Problem', 'Sub Cause', 'Root Cause', 'Solution', 'Action Needed', 'Responsible', 'Timeline'];
        $sheet->fromArray($headers, null, 'A1');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

        // Add sample data
        $sampleData = [
            ['Low vaccination coverage in remote areas', 'Outreach teams cannot reach scattered settlements', 'Difficult terrain and few outreach visits', 'Deploy mobile vaccination teams', 'Schedule weekly visits to remote villages', 'District Health Officer', '2 weeks'],
            ['Vaccine hesitancy among parents', 'Rumours circulating within the community', 'Misinformation and lack of awareness', 'Community awareness sessions', 'Conduct awareness campaigns with religious leaders', 'Community Health Workers', '1 month'],
            ['Cold chain maintenance issues', 'Frequent power outages at the facility', 'Ageing refrigeration equipment', 'Upgrade refrigeration equipment', 'Procure new vaccine refrigerators', 'Logistics Manager', '3 weeks'],
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set sheet title
        $sheet->setTitle('Action Plan Template');

        // Create the response
        $writer = new Xlsx($spreadsheet);

        $filename = 'action_plan_sample_template.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
