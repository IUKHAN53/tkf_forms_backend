<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarrierCategory;
use App\Models\BridgingTheGapTeamMember;
use App\Models\FgdsHealthWorkers;
use App\Models\FgdsHealthWorkersBarrier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            'total_barriers' => FgdsHealthWorkersBarrier::count(),
            'total_participants' => FgdsHealthWorkers::selectRaw('SUM(participants_males + participants_females) as total')->value('total') ?? 0,
            'total_males' => FgdsHealthWorkers::sum('participants_males') ?? 0,
            'total_females' => FgdsHealthWorkers::sum('participants_females') ?? 0,
            'ucs_covered' => FgdsHealthWorkers::distinct('uc')->count('uc'),
        ];

        // Get barriers count by category for statistics
        $barriersByCategory = FgdsHealthWorkersBarrier::select('barrier_category_id', DB::raw('count(*) as count'))
            ->groupBy('barrier_category_id')
            ->pluck('count', 'barrier_category_id')
            ->toArray();

        $categories = BarrierCategory::ordered();
        $stats['barriers_by_category'] = $categories->map(function ($cat) use ($barriersByCategory) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'count' => $barriersByCategory[$cat->id] ?? 0,
            ];
        });

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
        $fgdsHealthWorker->load(['participants', 'barriers.category']);
        return view('admin.core-forms.fgds-health-workers.show', compact('fgdsHealthWorker'));
    }

    public function edit(FgdsHealthWorkers $fgdsHealthWorker)
    {
        $fgdsHealthWorker->load('participants');
        return view('admin.core-forms.fgds-health-workers.edit', compact('fgdsHealthWorker'));
    }

    public function update(Request $request, FgdsHealthWorkers $fgdsHealthWorker)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'district' => 'required|string|max:255',
            'uc' => 'required|string|max:255',
            'hfs' => 'required|string|max:255',
            'group_type' => 'nullable|string|max:255',
            'facilitator_tkf' => 'nullable|string|max:255',
            'facilitator_govt' => 'nullable|string|max:255',
            'participants_males' => 'required|integer|min:0',
            'participants_females' => 'required|integer|min:0',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $fgdsHealthWorker->update($validated);

        return redirect()->route('admin.fgds-health-workers.show', $fgdsHealthWorker)
            ->with('success', 'FGDs-Health Workers session updated successfully.');
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

        // Parse the Excel file and extract barriers
        try {
            $importResult = $this->parseAndStoreBarriers($record, $file->getRealPath());
            $message = "Barriers file uploaded successfully for record {$record->unique_id}. ";
            $message .= "Imported {$importResult['imported']} barriers.";
            if ($importResult['skipped'] > 0) {
                $message .= " Skipped {$importResult['skipped']} empty rows.";
            }
            if (isset($importResult['new_categories']) && $importResult['new_categories'] > 0) {
                $message .= " Created {$importResult['new_categories']} new categories.";
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.fgds-health-workers.index')
                ->with('error', "File uploaded but failed to parse barriers: " . $e->getMessage());
        }

        return redirect()->route('admin.fgds-health-workers.index')
            ->with('success', $message);
    }

    /**
     * Parse Excel file and store barriers
     */
    private function parseAndStoreBarriers(FgdsHealthWorkers $record, string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Get all barrier categories indexed by name (normalized for matching)
        $categories = BarrierCategory::all()->keyBy(function ($cat) {
            return $this->normalizeCategory($cat->name);
        });

        // Delete existing barriers for this record before importing new ones
        FgdsHealthWorkersBarrier::where('fgds_health_workers_id', $record->id)->delete();

        $imported = 0;
        $skipped = 0;
        $newCategories = 0;

        // Skip header row (index 0), process data rows
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Skip header

            // Expected format: Sr. No | Identified Barriers | Category
            $serialNumber = trim($row[0] ?? '');
            $barrierText = trim($row[1] ?? '');
            $categoryName = trim($row[2] ?? '');

            // Skip empty barrier rows
            if (empty($barrierText)) {
                $skipped++;
                continue;
            }

            // Find matching category
            $normalizedCategory = $this->normalizeCategory($categoryName);
            $category = $categories->get($normalizedCategory);

            if (!$category) {
                // Try partial match
                $category = $this->findCategoryByPartialMatch($categoryName, $categories);
            }

            if (!$category && !empty($categoryName)) {
                // Create new category if it doesn't exist
                $maxSortOrder = BarrierCategory::max('sort_order') ?? 0;
                $category = BarrierCategory::create([
                    'name' => $categoryName,
                    'sort_order' => $maxSortOrder + 1,
                ]);
                // Add to categories collection for subsequent rows
                $categories[$normalizedCategory] = $category;
                $newCategories++;
            }

            if (!$category) {
                // If still no category (empty category name), skip this row
                $skipped++;
                continue;
            }

            // Create the barrier record
            FgdsHealthWorkersBarrier::create([
                'fgds_health_workers_id' => $record->id,
                'barrier_category_id' => $category->id,
                'barrier_text' => $barrierText,
                'serial_number' => is_numeric($serialNumber) ? (int)$serialNumber : null,
            ]);

            $imported++;
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'new_categories' => $newCategories,
        ];
    }

    /**
     * Normalize category name for comparison
     */
    private function normalizeCategory(string $name): string
    {
        // Remove extra spaces, lowercase, remove trailing punctuation
        $normalized = strtolower(trim(preg_replace('/\s+/', ' ', $name)));
        // Remove trailing period or other punctuation
        $normalized = rtrim($normalized, '.,;:');
        return $normalized;
    }

    /**
     * Find category by partial match
     */
    private function findCategoryByPartialMatch(string $categoryName, $categories)
    {
        $normalized = $this->normalizeCategory($categoryName);

        foreach ($categories as $key => $category) {
            // Check if one contains the other
            if (str_contains($key, $normalized) || str_contains($normalized, $key)) {
                return $category;
            }

            // Check first few words match (at least first 2 words)
            $searchWords = explode(' ', $normalized);
            $categoryWords = explode(' ', $key);

            if (count($searchWords) >= 2 && count($categoryWords) >= 2) {
                if ($searchWords[0] === $categoryWords[0] && $searchWords[1] === $categoryWords[1]) {
                    return $category;
                }
            }

            // Check for keyword matches (e.g., "communication", "cultural", "service")
            $keywords = ['cultural', 'communication', 'service', 'system', 'client', 'provider', 'supplies', 'place', 'environment'];
            foreach ($keywords as $keyword) {
                if (str_contains($normalized, $keyword) && str_contains($key, $keyword)) {
                    return $category;
                }
            }
        }

        return null;
    }

    /**
     * Download sample barriers Excel template
     */
    public function barriersSample()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['Sr. No', 'Identified Barriers', 'Category'];
        $sheet->fromArray($headers, null, 'A1');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);

        // Get categories for reference
        $categories = BarrierCategory::ordered()->pluck('name')->toArray();

        // Add sample data with different categories
        $sampleData = [
            [1, 'Health workers lack proper training on vaccination protocols', $categories[0] ?? 'Cultural Compatibility / Traditional Beliefs and Practices.'],
            [2, 'No proper communication channels with community', $categories[1] ?? 'Communication / Information.'],
            [3, 'Insufficient vaccination supplies at health facility', $categories[2] ?? 'Service Availability.'],
            [4, 'Complex reporting procedures for vaccination data', $categories[3] ?? 'System and Procedures.'],
            [5, 'Limited interaction time with caregivers', $categories[4] ?? 'Client / Provider Relations.'],
            [6, 'Need for refresher training on cold chain management', $categories[5] ?? 'Provider Technical Competence.'],
            [7, 'Frequent stock-outs of vaccines', $categories[6] ?? 'Supplies and Equipment / Medicine.'],
            [8, 'Poor storage facilities for vaccines', $categories[7] ?? 'Place / Environment.'],
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        // Add empty rows for user to fill
        for ($i = 9; $i <= 20; $i++) {
            $sheet->setCellValue('A' . ($i + 1), $i);
        }

        // Auto-size columns
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(50);

        // Add categories reference sheet
        $categoriesSheet = $spreadsheet->createSheet();
        $categoriesSheet->setTitle('Categories Reference');
        $categoriesSheet->setCellValue('A1', 'Available Categories');
        $categoriesSheet->getStyle('A1')->getFont()->setBold(true);

        foreach ($categories as $index => $category) {
            $categoriesSheet->setCellValue('A' . ($index + 2), $category);
        }
        $categoriesSheet->getColumnDimension('A')->setWidth(60);

        // Set first sheet as active
        $spreadsheet->setActiveSheetIndex(0);
        $sheet->setTitle('Barriers Template');

        // Create the response
        $writer = new Xlsx($spreadsheet);

        $filename = 'barriers_health_workers_sample_template.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
