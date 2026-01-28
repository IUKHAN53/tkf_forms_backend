<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BridgingTheGap;
use App\Models\BridgingTheGapActionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
        $bridgingTheGap->load(['participants', 'teamMembers.participant', 'user']);
        return view('admin.core-forms.bridging-the-gap.show', compact('bridgingTheGap'));
    }

    public function edit(BridgingTheGap $bridgingTheGap)
    {
        $bridgingTheGap->load(['participants', 'teamMembers.participant', 'user']);
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
        ]);

        $bridgingTheGap->update($validated);

        return redirect()->route('admin.bridging-the-gap.show', $bridgingTheGap)
            ->with('success', 'Bridging The Gap record updated successfully.');
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

        // Parse the Excel file and extract action plans
        try {
            $importResult = $this->parseAndStoreActionPlans($record, $file->getRealPath());
            $message = "Action plan uploaded successfully for record {$record->unique_id}. ";
            $message .= "Imported {$importResult['imported']} action items.";
            if ($importResult['skipped'] > 0) {
                $message .= " Skipped {$importResult['skipped']} empty rows.";
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.bridging-the-gap.index')
                ->with('error', "File uploaded but failed to parse action plans: " . $e->getMessage());
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

        // Delete existing action plans for this record before importing new ones
        BridgingTheGapActionPlan::where('bridging_the_gap_id', $record->id)->delete();

        $imported = 0;
        $skipped = 0;
        $serialNumber = 1;

        // Skip header row (index 0), process data rows
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Skip header

            // Expected format: Problem | Solution | Action Needed | Who is Responsible | Timeline
            $problem = trim($row[0] ?? '');
            $solution = trim($row[1] ?? '');
            $actionNeeded = trim($row[2] ?? '');
            $whoIsResponsible = trim($row[3] ?? '');
            $timeline = trim($row[4] ?? '');

            // Skip empty problem rows (problem is required)
            if (empty($problem)) {
                $skipped++;
                continue;
            }

            // Create the action plan record
            BridgingTheGapActionPlan::create([
                'bridging_the_gap_id' => $record->id,
                'problem' => $problem,
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
     * Download sample action plan Excel template
     */
    public function actionPlanSample()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['Problem', 'Solution', 'Action Needed', 'Who is Responsible', 'Timeline'];
        $sheet->fromArray($headers, null, 'A1');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        // Add sample data
        $sampleData = [
            ['Low vaccination coverage in remote areas', 'Deploy mobile vaccination teams', 'Schedule weekly visits to remote villages', 'District Health Officer', '2 weeks'],
            ['Vaccine hesitancy among parents', 'Community awareness sessions', 'Conduct awareness campaigns with religious leaders', 'Community Health Workers', '1 month'],
            ['Cold chain maintenance issues', 'Upgrade refrigeration equipment', 'Procure new vaccine refrigerators', 'Logistics Manager', '3 weeks'],
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        // Auto-size columns
        foreach (range('A', 'E') as $col) {
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
