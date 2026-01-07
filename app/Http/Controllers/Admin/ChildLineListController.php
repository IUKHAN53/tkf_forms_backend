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
            'father_cnic' => 'nullable|string|max:255',
            'house_number' => 'nullable|string|max:255',
            'address' => 'required|string',
            'guardian_phone' => 'nullable|string|max:255',
            'type' => 'required|in:Zero Dose,Defaulter',
            'missed_vaccines' => 'required|array|min:1',
            'reasons_of_missing' => 'required|string|max:255',
            'plan_for_coverage' => 'required|string',
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
            'father_cnic' => 'nullable|string|max:255',
            'house_number' => 'nullable|string|max:255',
            'address' => 'required|string',
            'guardian_phone' => 'nullable|string|max:255',
            'type' => 'required|in:Zero Dose,Defaulter',
            'missed_vaccines' => 'required|array|min:1',
            'reasons_of_missing' => 'required|string|max:255',
            'plan_for_coverage' => 'required|string',
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

        $columns = ['DIVISION', 'DISTRICT', 'TOWN', 'UC', 'OUTREACH', 'CHILD NAME', 'FATHER NAME', 'GENDER', 'DATE OF BIRTH', 'AGE IN MONTHS', 'FATHER CNIC/B-FORM', 'HOUSE #', 'ADDRESS/ LOCATION', 'PHONE # OF GUARDIAN', 'TYPE ( ZD/DEFAULTER)', 'MISSED VACCINE', 'REASONS OF MISSING', 'PLAN FOR COVERAGE'];

        $callback = function () use ($records, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($records as $item) {
                fputcsv($file, [
                    $item->division,
                    $item->district,
                    $item->town,
                    $item->uc,
                    $item->outreach,
                    $item->child_name,
                    $item->father_name,
                    ucfirst($item->gender),
                    $item->date_of_birth?->format('Y-m-d'),
                    $item->age_in_months,
                    $item->father_cnic,
                    $item->house_number,
                    $item->address,
                    $item->guardian_phone,
                    $item->type,
                    is_array($item->missed_vaccines) ? implode(', ', $item->missed_vaccines) : $item->missed_vaccines,
                    $item->reasons_of_missing,
                    $item->plan_for_coverage,
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

        $columns = ['DIVISION', 'DISTRICT', 'TOWN', 'UC', 'OUTREACH', 'CHILD NAME', 'FATHER NAME', 'GENDER', 'DATE OF BIRTH', 'AGE IN MONTHS', 'FATHER CNIC/B-FORM', 'HOUSE #', 'ADDRESS/ LOCATION', 'PHONE # OF GUARDIAN', 'TYPE ( ZD/DEFAULTER)', 'MISSED VACCINE', 'REASONS OF MISSING', 'PLAN FOR COVERAGE'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['Karachi', 'Karachi Central', 'Liaquatabad', 'UC-1', 'Outreach Site 1', 'Ali Ahmed', 'Ahmed Khan', 'Male', '2024-01-15', '12', '12345-1234567-1', 'House 123', 'Street 5, Block A', '03001234567', 'Zero Dose', 'BCG, OPV0, HepB', 'Refusal', 'Follow-up visit scheduled']);
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
            if (count($row) < 18 || empty($row[0])) continue;

            try {
                // Parse missed vaccines from comma-separated string to array
                $missedVaccines = !empty($row[15]) ? array_map('trim', explode(',', $row[15])) : [];

                ChildLineList::create([
                    'division' => $row[0],
                    'district' => $row[1],
                    'town' => $row[2],
                    'uc' => $row[3],
                    'outreach' => $row[4],
                    'child_name' => $row[5],
                    'father_name' => $row[6],
                    'gender' => strtolower($row[7]),
                    'date_of_birth' => $row[8],
                    'age_in_months' => (int) $row[9],
                    'father_cnic' => $row[10],
                    'house_number' => $row[11],
                    'address' => $row[12],
                    'guardian_phone' => $row[13],
                    'type' => $row[14],
                    'missed_vaccines' => $missedVaccines,
                    'reasons_of_missing' => $row[16],
                    'plan_for_coverage' => $row[17],
                    'user_id' => auth()->id(),
                    'submitted_at' => now(),
                    'started_at' => now(),
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

        return redirect()->route('admin.child-line-list.index')
            ->with('success', $message);
    }
}
