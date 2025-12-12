<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AreaMappingController extends Controller
{
    public function index(Request $request)
    {
        $query = AreaMapping::query()->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('district', 'like', "%{$search}%")
                    ->orWhere('uc_name', 'like', "%{$search}%")
                    ->orWhere('tehsil', 'like', "%{$search}%")
                    ->orWhere('area_name', 'like', "%{$search}%");
            });
        }

        $mappings = $query->paginate(15)->withQueryString();

        // Prepare map data
        $mapData = AreaMapping::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($mapping) {
                return [
                    'lat' => (float) $mapping->latitude,
                    'lon' => (float) $mapping->longitude,
                    'popup' => "<strong>{$mapping->area_name}</strong><br>
                                District: {$mapping->district}<br>
                                UC: {$mapping->uc_name}<br>
                                Population: {$mapping->total_population}"
                ];
            })
            ->values()
            ->toArray();

        return view('admin.core-forms.area-mappings.index', compact('mappings', 'mapData'));
    }

    public function show(AreaMapping $areaMapping)
    {
        return view('admin.core-forms.area-mappings.show', compact('areaMapping'));
    }

    public function destroy(AreaMapping $areaMapping)
    {
        $areaMapping->delete();
        return redirect()->route('admin.area-mappings.index')
            ->with('success', 'Area mapping deleted successfully.');
    }

    public function export()
    {
        $mappings = AreaMapping::all();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="area_mappings_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['Form ID', 'District', 'Town', 'UC Name', 'Fix Site', 'Outreach Name', 'Area Name', 'Total Population', 'Total Under 2 Years', 'Total Zero Dose', 'Total Defaulter', 'Total Refusal', 'Latitude', 'Longitude', 'Created At'];

        $callback = function () use ($mappings, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($mappings as $mapping) {
                fputcsv($file, [
                    $mapping->unique_id,
                    $mapping->district,
                    $mapping->town,
                    $mapping->uc_name,
                    $mapping->fix_site,
                    $mapping->outreach_name,
                    $mapping->area_name,
                    $mapping->total_population,
                    $mapping->total_under_2_years,
                    $mapping->total_zero_dose,
                    $mapping->total_defaulter,
                    $mapping->total_refusal,
                    $mapping->latitude,
                    $mapping->longitude,
                    $mapping->created_at,
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
            'Content-Disposition' => 'attachment; filename="area_mappings_template.csv"',
        ];

        $columns = ['district', 'town', 'uc_name', 'fix_site', 'outreach_name', 'area_name', 'assigned_aic', 'assigned_cm', 'total_population', 'total_under_2_years', 'total_zero_dose', 'total_defaulter', 'total_refusal', 'latitude', 'longitude'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Add sample row
            fputcsv($file, ['District Name', 'Town Name', 'UC Name', 'Fix Site', 'Outreach Name', 'Area Name', 'AIC Name', 'CM Name', '1000', '100', '10', '5', '3', '31.5204', '74.3587']);
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
            if (count($row) < 13) continue;

            try {
                AreaMapping::create([
                    'district' => $row[0],
                    'town' => $row[1],
                    'uc_name' => $row[2],
                    'fix_site' => $row[3],
                    'outreach_name' => $row[4],
                    'area_name' => $row[5],
                    'assigned_aic' => $row[6],
                    'assigned_cm' => $row[7],
                    'total_population' => (int) $row[8],
                    'total_under_2_years' => (int) $row[9],
                    'total_zero_dose' => (int) $row[10],
                    'total_defaulter' => (int) $row[11],
                    'total_refusal' => (int) $row[12],
                    'latitude' => $row[13] ?? null,
                    'longitude' => $row[14] ?? null,
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

        return redirect()->route('admin.area-mappings.index')
            ->with('success', $message);
    }
}
