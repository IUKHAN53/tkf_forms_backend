<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BridgingTheGapTeamMember;
use App\Models\FgdsHealthWorkers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class FgdsHealthWorkersController extends Controller
{
    public function index(Request $request)
    {
        $query = FgdsHealthWorkers::with('participants')->latest();

        // Text search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('uc', 'like', "%{$search}%")
                    ->orWhere('hfs', 'like', "%{$search}%")
                    ->orWhere('facilitator_tkf', 'like', "%{$search}%");
            });
        }

        // UC filter
        if ($request->filled('uc')) {
            $query->where('uc', $request->uc);
        }

        // Group type filter
        if ($request->filled('group_type')) {
            $query->where('group_type', $request->group_type);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Facilitator filter
        if ($request->filled('facilitator')) {
            $query->where('facilitator_tkf', 'like', "%{$request->facilitator}%");
        }

        $fgdsHealthWorkers = $query->paginate(15)->withQueryString();

        // Get distinct values for filter dropdowns
        $ucs = FgdsHealthWorkers::distinct()->pluck('uc')->filter()->sort()->values();
        $groupTypes = FgdsHealthWorkers::distinct()->pluck('group_type')->filter()->sort()->values();

        // Calculate statistics
        $stats = [
            'total' => FgdsHealthWorkers::count(),
            'total_barriers' => 0, // TODO: Implement barriers count
            'total_participants' => FgdsHealthWorkers::selectRaw('SUM(participants_males + participants_females) as total')->value('total') ?? 0,
            'total_males' => FgdsHealthWorkers::sum('participants_males') ?? 0,
            'total_females' => FgdsHealthWorkers::sum('participants_females') ?? 0,
            'ucs_covered' => FgdsHealthWorkers::distinct('uc')->count('uc'),
        ];

        // Prepare map data
        $mapData = FgdsHealthWorkers::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($record) {
                return [
                    'lat' => (float) $record->latitude,
                    'lon' => (float) $record->longitude,
                    'popup' => "<strong>{$record->date}</strong><br>
                                UC: {$record->uc}<br>
                                HFS: {$record->hfs}<br>
                                Group Type: {$record->group_type}"
                ];
            })
            ->values()
            ->toArray();

        return view('admin.core-forms.fgds-health-workers.index', compact('fgdsHealthWorkers', 'mapData', 'ucs', 'groupTypes', 'stats'));
    }

    public function show(FgdsHealthWorkers $fgdsHealthWorker)
    {
        $fgdsHealthWorker->load('participants');
        return view('admin.core-forms.fgds-health-workers.show', compact('fgdsHealthWorker'));
    }

    public function destroy(FgdsHealthWorkers $fgdsHealthWorker)
    {
        // Get participant IDs before deleting
        $participantIds = $fgdsHealthWorker->participants()->pluck('id');

        // Delete team member references in Bridging The Gap forms
        if ($participantIds->isNotEmpty()) {
            BridgingTheGapTeamMember::whereIn('participant_id', $participantIds)->delete();
        }

        $fgdsHealthWorker->participants()->delete();
        $fgdsHealthWorker->delete();
        return redirect()->route('admin.fgds-health-workers.index')
            ->with('success', 'FGDs-Health Workers session deleted successfully.');
    }

    public function export()
    {
        $records = FgdsHealthWorkers::with('participants')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="fgds_health_workers_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['ID', 'District', 'UC Name', 'Session Date', 'Facilitator TKF', 'Facility Name', 'Facility Type', 'Barriers Identified', 'Solutions Proposed', 'Follow Up Actions', 'Participants Count', 'Latitude', 'Longitude', 'Created At'];

        $callback = function () use ($records, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($records as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->district,
                    $item->uc_name,
                    $item->session_date,
                    $item->facilitator_tkf,
                    $item->facility_name,
                    $item->facility_type,
                    $item->barriers_identified,
                    $item->solutions_proposed,
                    $item->follow_up_actions,
                    $item->participants->count(),
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
            'Content-Disposition' => 'attachment; filename="fgds_health_workers_template.csv"',
        ];

        $columns = ['district', 'uc_name', 'session_date', 'facilitator_tkf', 'facility_name', 'facility_type', 'barriers_identified', 'solutions_proposed', 'follow_up_actions', 'latitude', 'longitude'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['District Name', 'UC Name', '2025-01-15', 'Facilitator Name', 'Facility Name', 'Hospital', 'Barrier 1, Barrier 2', 'Solution 1, Solution 2', 'Follow up 1', '31.5204', '74.3587']);
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
            if (count($row) < 11) continue;

            try {
                FgdsHealthWorkers::create([
                    'district' => $row[0],
                    'uc_name' => $row[1],
                    'session_date' => $row[2],
                    'facilitator_tkf' => $row[3],
                    'facility_name' => $row[4],
                    'facility_type' => $row[5],
                    'barriers_identified' => $row[6],
                    'solutions_proposed' => $row[7],
                    'follow_up_actions' => $row[8],
                    'latitude' => $row[9] ?: null,
                    'longitude' => $row[10] ?: null,
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

        return redirect()->route('admin.fgds-health-workers.index')
            ->with('success', $message);
    }

    public function uploadBarriers(Request $request, $id)
    {
        $request->validate([
            'barriers_file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        $record = FgdsHealthWorkers::findOrFail($id);

        // Store the file
        $file = $request->file('barriers_file');
        $filename = 'barriers_' . $record->unique_id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('barriers/fgds_health_workers', $filename, 'public');

        // Update the record with the file path
        $record->update([
            'barriers_file' => $path,
        ]);

        return redirect()->route('admin.fgds-health-workers.index')
            ->with('success', "Barriers file uploaded successfully for record {$record->unique_id}.");
    }
}
