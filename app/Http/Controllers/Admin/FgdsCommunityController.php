<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BridgingTheGapTeamMember;
use App\Models\FgdsCommunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class FgdsCommunityController extends Controller
{
    public function index(Request $request)
    {
        $query = FgdsCommunity::with('participants')->latest();

        // Text search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('district', 'like', "%{$search}%")
                    ->orWhere('uc', 'like', "%{$search}%")
                    ->orWhere('facilitator_tkf', 'like', "%{$search}%")
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

        // Facilitator filter
        if ($request->filled('facilitator')) {
            $query->where('facilitator_tkf', 'like', "%{$request->facilitator}%");
        }

        $fgdsCommunity = $query->paginate(15)->withQueryString();

        // Get distinct values for filter dropdowns
        $districts = FgdsCommunity::distinct()->pluck('district')->filter()->sort()->values();
        $ucs = FgdsCommunity::distinct()->pluck('uc')->filter()->sort()->values();

        // Calculate statistics
        $stats = [
            'total' => FgdsCommunity::count(),
            'total_barriers' => 0, // TODO: Implement barriers count
            'total_participants' => FgdsCommunity::selectRaw('SUM(participants_males + participants_females) as total')->value('total') ?? 0,
            'total_males' => FgdsCommunity::sum('participants_males') ?? 0,
            'total_females' => FgdsCommunity::sum('participants_females') ?? 0,
            'districts_covered' => FgdsCommunity::distinct('district')->count('district'),
        ];

        // Prepare map data
        $mapData = FgdsCommunity::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($record) {
                $communities = is_array($record->community)
                    ? implode(', ', $record->community)
                    : ($record->community ?? '');

                return [
                    'lat' => (float) $record->latitude,
                    'lon' => (float) $record->longitude,
                    'popup' => "<strong>{$record->date}</strong><br>
                                District: {$record->district}<br>
                                UC: {$record->uc}<br>
                                Venue: {$record->venue}<br>
                                Community: {$communities}"
                ];
            })
            ->values()
            ->toArray();

        return view('admin.core-forms.fgds-community.index', compact('fgdsCommunity', 'mapData', 'districts', 'ucs', 'stats'));
    }

    public function show(FgdsCommunity $fgdsCommunity)
    {
        $fgdsCommunity->load('participants');
        return view('admin.core-forms.fgds-community.show', compact('fgdsCommunity'));
    }

    public function destroy(FgdsCommunity $fgdsCommunity)
    {
        // Get participant IDs before deleting
        $participantIds = $fgdsCommunity->participants()->pluck('id');

        // Delete team member references in Bridging The Gap forms
        if ($participantIds->isNotEmpty()) {
            BridgingTheGapTeamMember::whereIn('participant_id', $participantIds)->delete();
        }

        $fgdsCommunity->participants()->delete();
        $fgdsCommunity->delete();
        return redirect()->route('admin.fgds-community.index')
            ->with('success', 'FGDs-Community session deleted successfully.');
    }

    public function export()
    {
        $records = FgdsCommunity::with('participants')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="fgds_community_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['ID', 'District', 'UC Name', 'Session Date', 'Facilitator TKF', 'Venue', 'EPI Focal Person', 'Barriers Identified', 'Solutions Proposed', 'Follow Up Actions', 'Participants Count', 'Latitude', 'Longitude', 'Created At'];

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
                    $item->venue,
                    $item->epi_focal_person,
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
            'Content-Disposition' => 'attachment; filename="fgds_community_template.csv"',
        ];

        $columns = ['district', 'uc_name', 'session_date', 'facilitator_tkf', 'venue', 'epi_focal_person', 'barriers_identified', 'solutions_proposed', 'follow_up_actions', 'latitude', 'longitude'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['District Name', 'UC Name', '2025-01-15', 'Facilitator Name', 'Venue', 'EPI Focal Person', 'Barrier 1, Barrier 2', 'Solution 1, Solution 2', 'Follow up 1', '31.5204', '74.3587']);
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
                FgdsCommunity::create([
                    'district' => $row[0],
                    'uc_name' => $row[1],
                    'session_date' => $row[2],
                    'facilitator_tkf' => $row[3],
                    'venue' => $row[4],
                    'epi_focal_person' => $row[5],
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

        return redirect()->route('admin.fgds-community.index')
            ->with('success', $message);
    }

    public function uploadBarriers(Request $request, $id)
    {
        $request->validate([
            'barriers_file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        $record = FgdsCommunity::findOrFail($id);

        // Store the file
        $file = $request->file('barriers_file');
        $filename = 'barriers_' . $record->unique_id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('barriers/fgds_community', $filename, 'public');

        // Update the record with the file path
        $record->update([
            'barriers_file' => $path,
        ]);

        return redirect()->route('admin.fgds-community.index')
            ->with('success', "Barriers file uploaded successfully for record {$record->unique_id}.");
    }
}
