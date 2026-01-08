<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BridgingTheGap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BridgingTheGapController extends Controller
{
    public function index(Request $request)
    {
        // Use withCount instead of eager loading participant relationship to avoid issues with deleted participants
        $query = BridgingTheGap::with(['participants', 'teamMembers', 'user'])->latest();

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

        $records = $query->paginate(15)->withQueryString();

        // Get distinct values for filter dropdowns
        $districts = BridgingTheGap::distinct()->pluck('district')->filter()->sort()->values();
        $ucs = BridgingTheGap::distinct()->pluck('uc')->filter()->sort()->values();

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

        return view('admin.core-forms.bridging-the-gap.index', compact('records', 'mapData', 'districts', 'ucs'));
    }

    public function show(BridgingTheGap $bridgingTheGap)
    {
        $bridgingTheGap->load(['participants', 'teamMembers.participant', 'user']);
        return view('admin.core-forms.bridging-the-gap.show', compact('bridgingTheGap'));
    }

    public function destroy(BridgingTheGap $bridgingTheGap)
    {
        // Delete team members first (references to external participants)
        $bridgingTheGap->teamMembers()->delete();

        // Delete attendance participants (morphMany relationship)
        $bridgingTheGap->participants()->delete();

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

        return redirect()->route('admin.bridging-the-gap.index')
            ->with('success', "Action plan uploaded successfully for record {$record->unique_id}.");
    }
}
