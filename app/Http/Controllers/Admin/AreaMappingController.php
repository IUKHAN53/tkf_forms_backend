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

        return view('admin.core-forms.area-mappings.index', compact('mappings'));
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

        $columns = ['ID', 'District', 'UC Name', 'Tehsil', 'Area Name', 'Total Households', 'Total Children', 'Latitude', 'Longitude', 'Created At'];

        $callback = function () use ($mappings, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($mappings as $mapping) {
                fputcsv($file, [
                    $mapping->id,
                    $mapping->district,
                    $mapping->uc_name,
                    $mapping->tehsil,
                    $mapping->area_name,
                    $mapping->total_households,
                    $mapping->total_children,
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

        $columns = ['district', 'uc_name', 'tehsil', 'area_name', 'total_households', 'total_children', 'latitude', 'longitude'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Add sample row
            fputcsv($file, ['District Name', 'UC Name', 'Tehsil Name', 'Area Name', '100', '50', '31.5204', '74.3587']);
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
            if (count($row) < 8) continue;

            try {
                AreaMapping::create([
                    'district' => $row[0],
                    'uc_name' => $row[1],
                    'tehsil' => $row[2],
                    'area_name' => $row[3],
                    'total_households' => (int) $row[4],
                    'total_children' => (int) $row[5],
                    'latitude' => $row[6] ?: null,
                    'longitude' => $row[7] ?: null,
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
