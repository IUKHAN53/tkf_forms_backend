<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DraftList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DraftListController extends Controller
{
    public function index(Request $request)
    {
        $query = DraftList::query()->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('district', 'like', "%{$search}%")
                    ->orWhere('uc_name', 'like', "%{$search}%")
                    ->orWhere('child_name', 'like', "%{$search}%")
                    ->orWhere('father_name', 'like', "%{$search}%");
            });
        }

        $draftLists = $query->paginate(15)->withQueryString();

        return view('admin.core-forms.draft-lists.index', compact('draftLists'));
    }

    public function show(DraftList $draftList)
    {
        return view('admin.core-forms.draft-lists.show', compact('draftList'));
    }

    public function destroy(DraftList $draftList)
    {
        $draftList->delete();
        return redirect()->route('admin.draft-lists.index')
            ->with('success', 'Draft list entry deleted successfully.');
    }

    public function export()
    {
        $draftLists = DraftList::all();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="draft_lists_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['ID', 'District', 'UC Name', 'Vaccination Date', 'Child Name', 'Father Name', 'Age Months', 'Gender', 'Address', 'Vaccine Type', 'Dose Number', 'Vaccinator Name', 'Created At'];

        $callback = function () use ($draftLists, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($draftLists as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->district,
                    $item->uc_name,
                    $item->vaccination_date,
                    $item->child_name,
                    $item->father_name,
                    $item->age_months,
                    $item->gender,
                    $item->address,
                    $item->vaccine_type,
                    $item->dose_number,
                    $item->vaccinator_name,
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
            'Content-Disposition' => 'attachment; filename="draft_lists_template.csv"',
        ];

        $columns = ['district', 'uc_name', 'vaccination_date', 'child_name', 'father_name', 'age_months', 'gender', 'address', 'vaccine_type', 'dose_number', 'vaccinator_name'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['District Name', 'UC Name', '2025-01-15', 'Child Name', 'Father Name', '12', 'male', 'Address Here', 'OPV', '1', 'Vaccinator Name']);
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
                DraftList::create([
                    'district' => $row[0],
                    'uc_name' => $row[1],
                    'vaccination_date' => $row[2],
                    'child_name' => $row[3],
                    'father_name' => $row[4],
                    'age_months' => (int) $row[5],
                    'gender' => $row[6],
                    'address' => $row[7],
                    'vaccine_type' => $row[8],
                    'dose_number' => (int) $row[9],
                    'vaccinator_name' => $row[10],
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

        return redirect()->route('admin.draft-lists.index')
            ->with('success', $message);
    }
}
