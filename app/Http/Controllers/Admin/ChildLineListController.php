<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChildLineList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ChildLineListController extends Controller
{
    public function index(Request $request)
    {
        $query = ChildLineList::query()->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('district', 'like', "%{$search}%")
                    ->orWhere('uc', 'like', "%{$search}%")
                    ->orWhere('child_name', 'like', "%{$search}%")
                    ->orWhere('father_name', 'like', "%{$search}%");
            });
        }

        $childLineLists = $query->paginate(15)->withQueryString();

        // Prepare map data
        $mapData = ChildLineList::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($record) {
                return [
                    'lat' => (float) $record->latitude,
                    'lon' => (float) $record->longitude,
                    'popup' => "<strong>{$record->child_name}</strong><br>
                                District: {$record->district}<br>
                                UC: {$record->uc}<br>
                                Outreach: {$record->outreach}<br>
                                Type: {$record->type}"
                ];
            })
            ->values()
            ->toArray();

        return view('admin.core-forms.child-line-list.index', compact('childLineLists', 'mapData'));
    }

    public function create()
    {
        return view('admin.core-forms.child-line-list.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'division' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'town' => 'required|string|max:255',
            'uc' => 'required|string|max:255',
            'outreach' => 'required|string|max:255',
            'child_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date',
            'age_in_months' => 'required|integer|min:0',
            'vaccinator_name' => 'nullable|string|max:255',
            'iit_member_name' => 'nullable|string|max:255',
            'iit_member_contact' => 'nullable|string|max:255',
            'father_cnic' => 'nullable|string|max:255',
            'house_number' => 'nullable|string|max:255',
            'address' => 'required|string',
            'guardian_phone' => 'nullable|string|max:255',
            'type' => 'required|in:Zero Dose,Defaulter',
            'missed_vaccines' => 'required|array|min:1',
            'reasons_of_missing' => 'required|string|max:255',
            'plan_for_coverage' => 'required|string',
            'date_of_coverage' => 'nullable|date',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['submitted_at'] = now();
        $validated['started_at'] = now();

        ChildLineList::create($validated);

        return redirect()->route('admin.child-line-list.index')
            ->with('success', 'Child line list entry created successfully.');
    }

    public function edit(ChildLineList $childLineList)
    {
        return view('admin.core-forms.child-line-list.edit', compact('childLineList'));
    }

    public function update(Request $request, ChildLineList $childLineList)
    {
        $validated = $request->validate([
            'division' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'town' => 'required|string|max:255',
            'uc' => 'required|string|max:255',
            'outreach' => 'required|string|max:255',
            'child_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date',
            'age_in_months' => 'required|integer|min:0',
            'vaccinator_name' => 'nullable|string|max:255',
            'iit_member_name' => 'nullable|string|max:255',
            'iit_member_contact' => 'nullable|string|max:255',
            'father_cnic' => 'nullable|string|max:255',
            'house_number' => 'nullable|string|max:255',
            'address' => 'required|string',
            'guardian_phone' => 'nullable|string|max:255',
            'type' => 'required|in:Zero Dose,Defaulter',
            'missed_vaccines' => 'required|array|min:1',
            'reasons_of_missing' => 'required|string|max:255',
            'plan_for_coverage' => 'required|string',
            'date_of_coverage' => 'nullable|date',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $childLineList->update($validated);

        return redirect()->route('admin.child-line-list.index')
            ->with('success', 'Child line list entry updated successfully.');
    }

    public function show(ChildLineList $childLineList)
    {
        return view('admin.core-forms.child-line-list.show', compact('childLineList'));
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $deleted = ChildLineList::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.child-line-list.index')
            ->with('success', "{$deleted} record(s) deleted successfully.");
    }

    public function destroy(ChildLineList $childLineList)
    {
        $childLineList->delete();
        return redirect()->route('admin.child-line-list.index')
            ->with('success', 'Child line list entry deleted successfully.');
    }

    public function export()
    {
        $records = ChildLineList::all();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="child_line_list_' . date('Y-m-d') . '.csv"',
        ];

        $columns = [
            'DIVISION', 'DISTRICT', 'TOWN', 'UC', 'OUTREACH',
            'CHILD NAME', 'FATHER NAME', 'GENDER', 'DATE OF BIRTH', 'AGE IN MONTHS',
            'NAME OF VACCINATOR', 'NAME OF IIT TEAM MEMBER', 'CONTACT NUMBER OF IIT TEAM MEMBER',
            'FATHER CNIC/B-FORM', 'HOUSE #', 'ADDRESS/ LOCATION',
            'GPS Coordinates (To be fetched automatically)', 'PHONE # OF GUARDIAN',
            'TYPE ( ZD/DEFAULTER)', 'MISSED VACCINE', 'REASONS OF MISSING',
            'PLAN FOR COVERAGE', 'DATE OF COVERAGE',
        ];

        $callback = function () use ($records, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($records as $item) {
                $gps = $item->gps_coordinates;
                if (!$gps && $item->latitude && $item->longitude) {
                    $gps = $item->latitude . ',' . $item->longitude;
                }

                fputcsv($file, [
                    $item->division,
                    $item->district,
                    $item->town,
                    $item->uc,
                    $item->outreach,
                    $item->child_name,
                    $item->father_name,
                    ucfirst($item->gender),
                    $item->date_of_birth?->format('m/d/Y'),
                    $item->age_in_months,
                    $item->vaccinator_name,
                    $item->iit_member_name,
                    $item->iit_member_contact,
                    $item->father_cnic,
                    $item->house_number,
                    $item->address,
                    $gps,
                    $item->guardian_phone,
                    $item->type,
                    is_array($item->missed_vaccines) ? implode(', ', $item->missed_vaccines) : $item->missed_vaccines,
                    $item->reasons_of_missing,
                    $item->plan_for_coverage,
                    $item->date_of_coverage?->format('m/d/Y'),
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
            'Content-Disposition' => 'attachment; filename="child_line_list_template.csv"',
        ];

        $columns = [
            'DIVISION', 'DISTRICT', 'TOWN', 'UC', 'OUTREACH',
            'CHILD NAME', 'FATHER NAME', 'GENDER', 'DATE OF BIRTH', 'AGE IN MONTHS',
            'NAME OF VACCINATOR', 'NAME OF IIT TEAM MEMBER', 'CONTACT NUMBER OF IIT TEAM MEMBER',
            'FATHER CNIC/B-FORM', 'HOUSE #', 'ADDRESS/ LOCATION',
            'GPS Coordinates (To be fetched automatically)', 'PHONE # OF GUARDIAN',
            'TYPE ( ZD/DEFAULTER)', 'MISSED VACCINE', 'REASONS OF MISSING',
            'PLAN FOR COVERAGE', 'DATE OF COVERAGE',
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, [
                'Karachi', 'Karachi Central', 'Liaquatabad', 'UC-1', 'Outreach Site 1',
                'Ali Ahmed', 'Ahmed Khan', 'Male', '1/15/2024', '12',
                'Tayyaba Afridi', 'Ameen', '3181032760',
                '12345-1234567-1', 'House 123', 'Street 5, Block A',
                'to be fetched automatically from app', '3001234567',
                'Zero Dose', 'BCG, OPV0, HepB', 'Refusal',
                'Follow-up visit scheduled', '',
            ]);
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
        $rowNum = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            if (count($row) < 22 || empty($row[0])) continue;

            try {
                // Column mapping (0-indexed):
                // 0=DIVISION, 1=DISTRICT, 2=TOWN, 3=UC, 4=OUTREACH
                // 5=CHILD NAME, 6=FATHER NAME, 7=GENDER, 8=DOB, 9=AGE IN MONTHS
                // 10=VACCINATOR, 11=IIT MEMBER, 12=IIT CONTACT
                // 13=CNIC, 14=HOUSE#, 15=ADDRESS, 16=GPS, 17=PHONE
                // 18=TYPE, 19=MISSED VACCINE, 20=REASONS, 21=PLAN, 22=DATE OF COVERAGE

                $missedVaccines = !empty($row[19]) ? array_map('trim', explode(',', $row[19])) : [];

                // Parse GPS coordinates into lat/lng
                $latitude = null;
                $longitude = null;
                $gpsCoordinates = trim($row[16] ?? '');
                if ($gpsCoordinates && str_contains($gpsCoordinates, ',')) {
                    $parts = array_map('trim', explode(',', $gpsCoordinates));
                    if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                        $latitude = (float) $parts[0];
                        $longitude = (float) $parts[1];
                    }
                }

                // Parse date of birth - support multiple formats
                $dob = $row[8];
                if ($dob) {
                    $parsedDate = date_create($dob);
                    $dob = $parsedDate ? $parsedDate->format('Y-m-d') : $dob;
                }

                // Parse date of coverage
                $dateCoverage = isset($row[22]) && !empty(trim($row[22])) ? trim($row[22]) : null;
                if ($dateCoverage && !in_array(strtolower($dateCoverage), ['to be fetched from app', ''])) {
                    $parsedDate = date_create($dateCoverage);
                    $dateCoverage = $parsedDate ? $parsedDate->format('Y-m-d') : null;
                } else {
                    $dateCoverage = null;
                }

                ChildLineList::create([
                    'division' => $row[0],
                    'district' => $row[1],
                    'town' => $row[2],
                    'uc' => $row[3],
                    'outreach' => $row[4],
                    'child_name' => $row[5],
                    'father_name' => $row[6],
                    'gender' => strtolower($row[7]),
                    'date_of_birth' => $dob,
                    'age_in_months' => (int) $row[9],
                    'vaccinator_name' => $row[10] ?: null,
                    'iit_member_name' => $row[11] ?: null,
                    'iit_member_contact' => $row[12] ?: null,
                    'father_cnic' => $row[13] ?: null,
                    'house_number' => $row[14] ?: null,
                    'address' => $row[15],
                    'gps_coordinates' => ($latitude && $longitude) ? $gpsCoordinates : null,
                    'guardian_phone' => $row[17] ?: null,
                    'type' => $row[18],
                    'missed_vaccines' => $missedVaccines,
                    'reasons_of_missing' => $row[20],
                    'plan_for_coverage' => $row[21],
                    'date_of_coverage' => $dateCoverage,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'user_id' => auth()->id(),
                    'submitted_at' => now(),
                    'started_at' => now(),
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row {$rowNum}: " . $e->getMessage();
            }
        }

        fclose($handle);

        $message = "Successfully imported {$imported} records.";
        if (count($errors) > 0) {
            $message .= " " . count($errors) . " errors occurred.";
        }

        return redirect()->route('admin.child-line-list.index')
            ->with('success', $message);
    }
}
