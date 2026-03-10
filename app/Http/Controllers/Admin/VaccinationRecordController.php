<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VaccinationRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class VaccinationRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = VaccinationRecord::query()->with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('district', 'like', "%{$search}%")
                    ->orWhere('uc', 'like', "%{$search}%")
                    ->orWhere('child_name', 'like', "%{$search}%")
                    ->orWhere('father_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('vaccinated')) {
            $query->where('vaccinated', $request->vaccinated);
        }

        $records = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => VaccinationRecord::count(),
            'vaccinated' => VaccinationRecord::where('vaccinated', 'YES')->count(),
            'refusals' => VaccinationRecord::where('category', 'Refusal')->where('vaccinated', 'NO')->count(),
            'zero_dose' => VaccinationRecord::where('category', 'Zero Dose')->count(),
        ];

        $mapData = VaccinationRecord::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($record) {
                return [
                    'lat' => (float) $record->latitude,
                    'lon' => (float) $record->longitude,
                    'popup' => "<strong>{$record->child_name}</strong><br>
                                Father: {$record->father_name}<br>
                                District: {$record->district}<br>
                                UC: {$record->uc}<br>
                                Category: {$record->category}<br>
                                Vaccinated: {$record->vaccinated}"
                ];
            })
            ->values()
            ->toArray();

        return view('admin.core-forms.vaccination-records.index', compact('records', 'stats', 'mapData'));
    }

    public function show(VaccinationRecord $vaccinationRecord)
    {
        $vaccinationRecord->load('user');
        return view('admin.core-forms.vaccination-records.show', compact('vaccinationRecord'));
    }

    public function destroy(VaccinationRecord $vaccinationRecord)
    {
        $vaccinationRecord->delete();
        return redirect()->route('admin.vaccination-records.index')
            ->with('success', 'Vaccination record deleted successfully.');
    }

    public function export()
    {
        $records = VaccinationRecord::all();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="vaccination_records_' . date('Y-m-d') . '.csv"',
        ];

        $columns = [
            'Sr. No', 'Child Name', 'Father Name', 'Age', 'Address', 'Contact Number',
            'Category', 'Vaccinated', 'Date of Vaccination', 'Community Member Name',
            'Community Member Contact', 'GPS Coordinates', 'Fix Site', 'UC', 'District',
            'Submitted By', 'Submitted At',
        ];

        $callback = function () use ($records, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($records as $item) {
                fputcsv($file, [
                    $item->serial_number,
                    $item->child_name,
                    $item->father_name,
                    $item->age,
                    $item->address,
                    $item->contact_number,
                    $item->category,
                    $item->vaccinated,
                    $item->date_of_vaccination?->format('Y-m-d'),
                    $item->community_member_name,
                    $item->community_member_contact,
                    $item->gps_coordinates,
                    $item->fix_site,
                    $item->uc,
                    $item->district,
                    $item->user?->name ?? 'N/A',
                    $item->submitted_at?->format('Y-m-d H:i'),
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
            'Content-Disposition' => 'attachment; filename="vaccination_records_template.csv"',
        ];

        $columns = [
            'Child Name', 'Father Name', 'Age', 'Address', 'Contact Number',
            'Category (Defaulter/Refusal/Zero Dose)', 'Vaccinated (YES/NO)',
            'Date of Vaccination', 'Community Member Name', 'Community Member Contact',
            'GPS Coordinates', 'Fix Site', 'UC', 'District',
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, [
                'Ali Ahmed', 'Ahmed Khan', '2 years', 'Street 5 Block A',
                '03001234567', 'Refusal', 'NO', '', 'Rashid Khan',
                '03009876543', '24.8607, 67.0011', 'Site A', 'UC-1', 'Karachi Central',
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

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 7 || empty($row[0])) continue;

            try {
                VaccinationRecord::create([
                    'child_name' => $row[0],
                    'father_name' => $row[1],
                    'age' => $row[2] ?? null,
                    'address' => $row[3] ?? null,
                    'contact_number' => $row[4] ?? null,
                    'category' => $row[5] ?? 'Defaulter',
                    'vaccinated' => $row[6] ?? 'NO',
                    'date_of_vaccination' => !empty($row[7]) ? $row[7] : null,
                    'community_member_name' => $row[8] ?? null,
                    'community_member_contact' => $row[9] ?? null,
                    'gps_coordinates' => $row[10] ?? null,
                    'fix_site' => $row[11] ?? null,
                    'uc' => $row[12] ?? null,
                    'district' => $row[13] ?? null,
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

        return redirect()->route('admin.vaccination-records.index')
            ->with('success', $message);
    }
}
