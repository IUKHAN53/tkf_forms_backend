<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\BarrierCategory;
use App\Models\BridgingTheGapTeamMember;
use App\Models\FgdsCommunity;
use App\Models\FgdsCommunityBarrier;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FgdsCommunityController extends Controller
{
    public function index(Request $request)
    {
        // Page-wide filter: the same filters drive the table, the stat cards, the
        // barriers-by-category cards/modal and the map, so applying a filter
        // updates every count on the page — not just the table rows.
        $perPage = $request->input('per_page', 15);
        $fgdsCommunity = $this->applyBarrierListFilters(FgdsCommunity::with('participants'), $request)
            ->latest()
            ->paginate($perPage == 'all' ? 999999 : (int) $perPage)
            ->withQueryString();

        // IDs of every record matching the current filters (drives all counts).
        $filteredIds = $this->applyBarrierListFilters(FgdsCommunity::query(), $request)->pluck('id');

        // Get distinct values for filter dropdowns (always the full catalogue).
        $districts = FgdsCommunity::distinct()->pluck('district')->filter()->sort()->values();
        $ucs = FgdsCommunity::distinct()->pluck('uc')->filter()->sort()->values();
        $fixSites = FgdsCommunity::distinct()->pluck('fix_site')->filter()->sort()->values();

        // Statistics over the filtered set, from actual participant records.
        $participantsQuery = fn () => Participant::where('participantable_type', FgdsCommunity::class)
            ->whereIn('participantable_id', $filteredIds);
        $stats = [
            'total' => $filteredIds->count(),
            'total_barriers' => FgdsCommunityBarrier::whereIn('fgds_community_id', $filteredIds)->count(),
            'total_participants' => $participantsQuery()->count(),
            'total_males' => $participantsQuery()->where('gender', 'Male')->count(),
            'total_females' => $participantsQuery()->where('gender', 'Female')->count(),
            'districts_covered' => $this->applyBarrierListFilters(FgdsCommunity::query(), $request)
                ->distinct('district')->count('district'),
        ];

        // Barriers by category, restricted to the filtered records.
        $barriersByCategory = FgdsCommunityBarrier::whereIn('fgds_community_id', $filteredIds)
            ->select('barrier_category_id', DB::raw('count(*) as count'))
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

        // Prepare map data (also filtered, so the map matches the rest of the page)
        $mapData = $this->applyBarrierListFilters(FgdsCommunity::query(), $request)
            ->whereNotNull('latitude')
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

        return view('admin.core-forms.fgds-community.index', compact('fgdsCommunity', 'mapData', 'districts', 'ucs', 'fixSites', 'stats'));
    }

    /**
     * Apply the list-page filters (search, district, uc, fix site, date range,
     * facilitator)
     * to a query. Shared by the table, the stat counts, the barriers-by-category
     * cards and the category modal so the whole page reflects the same filter.
     */
    private function applyBarrierListFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('district', 'like', "%{$search}%")
                    ->orWhere('uc', 'like', "%{$search}%")
                    ->orWhere('facilitator_tkf', 'like', "%{$search}%")
                    ->orWhere('venue', 'like', "%{$search}%");
            });
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        if ($request->filled('uc')) {
            $query->where('uc', $request->uc);
        }

        if ($request->filled('fix_site')) {
            $query->where('fix_site', $request->fix_site);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('facilitator')) {
            $query->where('facilitator_tkf', 'like', "%{$request->facilitator}%");
        }

        return $query;
    }

    public function show(FgdsCommunity $fgdsCommunity)
    {
        $fgdsCommunity->load(['participants', 'barriers.category']);

        return view('admin.core-forms.fgds-community.show', compact('fgdsCommunity'));
    }

    /**
     * Drill-down for a barrier category card on the list page: returns every
     * FGDs-Community record carrying a barrier in the given category, with the
     * barrier texts, as JSON for the modal.
     */
    public function barriersByCategory(Request $request, BarrierCategory $category)
    {
        // Respect the list page's active filters (passed through as query string)
        // so the modal lists only the FGDs in the current filtered view.
        $records = $this->applyBarrierListFilters(FgdsCommunity::query(), $request)
            ->with(['barriers' => fn ($q) => $q->where('barrier_category_id', $category->id)->orderBy('serial_number')])
            ->whereHas('barriers', fn ($q) => $q->where('barrier_category_id', $category->id))
            ->latest()
            ->get()
            ->map(fn ($item) => [
                'id'          => $item->id,
                'unique_id'   => $item->unique_id,
                'date'        => $item->date ? $item->date->format('M d, Y') : 'N/A',
                'venue'       => $item->venue,
                'district'    => $item->district,
                'uc'          => DashboardController::getConsolidatedUcName($item->uc),
                'facilitator' => $item->facilitator_tkf,
                'barriers'    => $item->barriers->map(fn ($b) => [
                    'serial_number' => $b->serial_number,
                    'text'          => $b->barrier_text,
                ])->values(),
            ])
            ->values();

        return response()->json([
            'success'  => true,
            'category' => $category->name,
            'count'    => $records->sum(fn ($r) => count($r['barriers'])),
            'records'  => $records,
        ]);
    }

    public function edit(FgdsCommunity $fgdsCommunity)
    {
        $fgdsCommunity->load('participants');

        return view('admin.core-forms.fgds-community.edit', compact('fgdsCommunity'));
    }

    public function update(Request $request, FgdsCommunity $fgdsCommunity)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'district' => 'required|string|max:255',
            'uc' => 'required|string|max:255',
            'fix_site' => 'nullable|string|max:255',
            'venue' => 'required|string|max:255',
            'facilitator_tkf' => 'nullable|string|max:255',
            'facilitator_govt' => 'nullable|string|max:255',
            'participants_males' => 'required|integer|min:0',
            'participants_females' => 'required|integer|min:0',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $fgdsCommunity->update($validated);

        return redirect()->route('admin.fgds-community.show', $fgdsCommunity)
            ->with('success', 'FGDs-Community session updated successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        $records = FgdsCommunity::whereIn('id', $request->ids)->get();
        $deleted = 0;

        foreach ($records as $record) {
            $participantIds = $record->participants()->pluck('id');
            if ($participantIds->isNotEmpty()) {
                BridgingTheGapTeamMember::whereIn('participant_id', $participantIds)->delete();
            }
            $record->participants()->delete();
            $record->delete();
            $deleted++;
        }

        return redirect()->route('admin.fgds-community.index')
            ->with('success', "{$deleted} record(s) deleted successfully.");
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

    public function destroyBarrier(FgdsCommunityBarrier $barrier)
    {
        $recordId = $barrier->fgds_community_id;
        $barrier->delete();

        return redirect()->route('admin.fgds-community.show', $recordId)
            ->with('success', 'Barrier deleted successfully.');
    }

    public function export()
    {
        $records = FgdsCommunity::with('participants')->withCount('barriers')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="fgds_community_'.date('Y-m-d').'.csv"',
        ];

        $columns = ['ID', 'Form ID', 'District', 'UC', 'Fix Site', 'Outreach', 'Session Date', 'Facilitator TKF', 'Venue', 'Community', 'Barriers Identified', 'Participants Count', 'Males', 'Females', 'Latitude', 'Longitude', 'Created At'];

        $callback = function () use ($records, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($records as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->unique_id,
                    $item->district,
                    $item->uc,
                    $item->fix_site,
                    $item->outreach,
                    optional($item->date)->format('Y-m-d'),
                    $item->facilitator_tkf,
                    $item->venue,
                    is_array($item->community) ? implode(', ', $item->community) : $item->community,
                    $item->barriers_count,
                    $item->participants->count(),
                    $item->participants->where('gender', 'Male')->count(),
                    $item->participants->where('gender', 'Female')->count(),
                    $item->latitude,
                    $item->longitude,
                    optional($item->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * The writable CSV columns, in order: header label => [attribute, required].
     *
     * Drives template() and import() together so the file we hand out is the
     * file we can read back. export() adds read-only columns on top (ID,
     * Barriers Identified, Participants Count, Created At) which import ignores
     * — barriers and the participant attendance rows are child records and do
     * not come from this CSV.
     */
    private const IMPORT_FIELDS = [
        'Form ID' => ['unique_id', false],
        'District' => ['district', true],
        'UC' => ['uc', true],
        'Fix Site' => ['fix_site', true],
        'Outreach' => ['outreach', true],
        'Session Date' => ['date', true],
        'Facilitator TKF' => ['facilitator_tkf', true],
        'Venue' => ['venue', true],
        'Community' => ['community', true],
        'Males' => ['participants_males', false],
        'Females' => ['participants_females', false],
        'Latitude' => ['latitude', false],
        'Longitude' => ['longitude', false],
    ];

    /**
     * Legacy header spellings mapped onto their canonical normalized form, so
     * files saved from the previous template still import.
     */
    private const IMPORT_HEADER_ALIASES = [
        'ucname' => 'uc',
        'unioncouncil' => 'uc',
        'date' => 'sessiondate',
    ];

    public function template()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="fgds_community_template.csv"',
        ];

        // Form ID is left blank: the model stamps one on create.
        $sample = [
            '', 'Karachi', 'Gujro Zone C', 'Govt Dispensary Bilal Colony', 'Outreach A',
            '2026-01-15', 'Facilitator Name', 'Community Hall', 'Mohalla One, Mohalla Two',
            '6', '4', '24.9056', '67.0822',
        ];

        $callback = function () use ($sample) {
            $file = fopen('php://output', 'w');
            fputcsv($file, array_keys(self::IMPORT_FIELDS));
            fputcsv($file, $sample);
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $header = fgetcsv($handle);

        if ($header === false) {
            fclose($handle);

            return redirect()->route('admin.fgds-community.index')
                ->with('error', 'That file is empty.');
        }

        $map = $this->resolveImportColumns($header);

        // Nothing recognisable in the header row means positional guessing,
        // which is how every value used to end up in the wrong field.
        if ($map['uc'] === null && $map['venue'] === null) {
            fclose($handle);

            return redirect()->route('admin.fgds-community.index')
                ->with('error', 'Could not recognise the column headers. Download the template and keep its header row.');
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $line = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $line++;

            // Trailing blank lines are not worth reporting.
            if (count(array_filter($row, fn ($c) => trim((string) $c) !== '')) === 0) {
                continue;
            }

            $get = fn (string $attr) => ($map[$attr] !== null && isset($row[$map[$attr]]))
                ? trim((string) $row[$map[$attr]])
                : '';

            $missing = [];
            foreach (self::IMPORT_FIELDS as $label => [$attr, $required]) {
                if ($required && $get($attr) === '') {
                    $missing[] = $label;
                }
            }

            if ($missing) {
                $errors[] = "Row {$line}: missing ".implode(', ', $missing).'.';
                $skipped++;

                continue;
            }

            try {
                $date = Carbon::parse($get('date'));
            } catch (\Exception $e) {
                $errors[] = "Row {$line}: could not read the session date.";
                $skipped++;

                continue;
            }

            // A Form ID that already exists means the row is already in the
            // system — re-importing an export must not duplicate every record.
            $uniqueId = $get('unique_id');

            if ($uniqueId !== '' && FgdsCommunity::where('unique_id', $uniqueId)->exists()) {
                $errors[] = "Row {$line}: {$uniqueId} already exists.";
                $skipped++;

                continue;
            }

            $attributes = [
                'district' => $get('district'),
                'uc' => $get('uc'),
                'fix_site' => $get('fix_site'),
                'outreach' => $get('outreach'),
                'date' => $date,
                'facilitator_tkf' => $get('facilitator_tkf'),
                'venue' => $get('venue'),
                'community' => array_values(array_filter(array_map('trim', explode(',', $get('community'))))),
                'participants_males' => (int) $get('participants_males'),
                'participants_females' => (int) $get('participants_females'),
                'latitude' => $get('latitude') !== '' ? (float) $get('latitude') : null,
                'longitude' => $get('longitude') !== '' ? (float) $get('longitude') : null,
            ];

            if ($uniqueId !== '') {
                $attributes['unique_id'] = $uniqueId;
            }

            try {
                FgdsCommunity::create($attributes);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row {$line}: ".$e->getMessage();
                $skipped++;
            }
        }

        fclose($handle);

        return redirect()->route('admin.fgds-community.index')
            ->with('success', "Imported {$imported} record(s).".($skipped ? " Skipped {$skipped}." : ''))
            ->with('error', $this->importErrorSummary($errors));
        }

    /**
     * Match the header row to model attributes by name rather than position, so
     * a reordered or extra column cannot shift every value into the wrong field.
     */
    private function resolveImportColumns(array $header): array
    {
        $normalize = fn ($value) => preg_replace('/[^a-z0-9]/', '', strtolower((string) $value));

        $wanted = [];
        foreach (self::IMPORT_FIELDS as $label => [$attr, $required]) {
            $wanted[$normalize($label)] = $attr;
        }

        $map = [];
        foreach (self::IMPORT_FIELDS as [$attr, $required]) {
            $map[$attr] = null;
        }

        foreach ($header as $index => $cell) {
            $key = $normalize($cell);
            $key = self::IMPORT_HEADER_ALIASES[$key] ?? $key;

            if (isset($wanted[$key]) && $map[$wanted[$key]] === null) {
                $map[$wanted[$key]] = $index;
            }
        }

        return $map;
    }

    /**
     * Row-level problems are worth showing — the old import counted them and
     * threw the reasons away. A malformed file can produce hundreds, so cap the
     * list to keep the flash message readable.
     */
    private function importErrorSummary(array $errors): ?string
    {
        if (! $errors) {
            return null;
        }

        $shown = array_slice($errors, 0, 8);
        $summary = implode(' ', $shown);

        if (count($errors) > count($shown)) {
            $summary .= ' (+'.(count($errors) - count($shown)).' more)';
        }

        return $summary;
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

        // Parse the Excel file and extract barriers
        try {
            $importResult = $this->parseAndStoreBarriers($record, $file->getRealPath());
            $message = "Barriers file uploaded successfully for record {$record->unique_id}. ";
            $message .= "Imported {$importResult['imported']} barriers.";
            if ($importResult['skipped'] > 0) {
                $message .= " Skipped {$importResult['skipped']} empty rows.";
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.fgds-community.index')
                ->with('error', "File uploaded but failed to parse barriers: " . $e->getMessage());
        }

        return redirect()->route('admin.fgds-community.index')
            ->with('success', $message);
    }

    /**
     * Parse Excel file and store barriers
     */
    private function parseAndStoreBarriers(FgdsCommunity $record, string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Canonical 11 categories indexed by normalized name, reused for every row.
        // Imports map into these and NEVER create a new category.
        $categories = BarrierCategory::all()->keyBy(fn ($cat) => BarrierCategory::normalizeName($cat->name));

        // Delete existing barriers for this record before importing new ones
        FgdsCommunityBarrier::where('fgds_community_id', $record->id)->delete();

        $imported = 0;
        $skipped = 0;

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

            // Always resolves to one of the canonical 11 (closest match / fallback).
            $category = BarrierCategory::resolveForImport($categoryName, $categories);

            if (!$category) {
                // Only possible if the categories table is empty — skip defensively.
                $skipped++;
                continue;
            }

            // Create the barrier record
            FgdsCommunityBarrier::create([
                'fgds_community_id' => $record->id,
                'barrier_category_id' => $category->id,
                'barrier_text' => $barrierText,
                'serial_number' => is_numeric($serialNumber) ? (int)$serialNumber : null,
            ]);

            $imported++;
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
        ];
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
            [1, 'Some community members believe vaccines cause harm', $categories[0] ?? 'Misconceptions and Misinformation about Vaccines'],
            [2, 'Parents fear vaccines cause serious side effects', $categories[1] ?? 'Fear of Side Effects and Vaccine Safety Concerns'],
            [3, 'Vaccination carried out without proper consent', $categories[2] ?? 'Forceful Vaccination and Consent Issues'],
            [4, 'Health workers are not friendly to mothers', $categories[3] ?? 'Poor Behavior and Communication of Health Workers'],
            [5, 'Lack of awareness about vaccination schedule', $categories[4] ?? 'Lack of Community Awareness and Health Education'],
            [6, 'Community does not trust government vaccination drives', $categories[5] ?? 'Lack of Trust in Health System and Government'],
            [7, 'Vaccination center is too far and poorly equipped', $categories[6] ?? 'Inadequate Services at Health Facility and Infrastructure'],
            [8, 'No clean water or basic facilities in the community', $categories[7] ?? 'Lack of Essential Community Services'],
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

        $filename = 'barriers_sample_template.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
