<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthcareBarrier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class HealthcareBarrierController extends Controller
{
    public function index(Request $request)
    {
        $query = HealthcareBarrier::with('participants')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('district', 'like', "%{$search}%")
                    ->orWhere('uc_name', 'like', "%{$search}%")
                    ->orWhere('facilitator_tkf', 'like', "%{$search}%")
                    ->orWhere('facility_name', 'like', "%{$search}%");
            });
        }

        $healthcareBarriers = $query->paginate(15)->withQueryString();

        return view('admin.core-forms.healthcare-barriers.index', compact('healthcareBarriers'));
    }

    public function show(HealthcareBarrier $healthcareBarrier)
    {
        $healthcareBarrier->load('participants');
        return view('admin.core-forms.healthcare-barriers.show', compact('healthcareBarrier'));
    }

    public function destroy(HealthcareBarrier $healthcareBarrier)
    {
        $healthcareBarrier->participants()->delete();
        $healthcareBarrier->delete();
        return redirect()->route('admin.healthcare-barriers.index')
            ->with('success', 'Healthcare barrier session deleted successfully.');
    }

    public function export()
    {
        $healthcareBarriers = HealthcareBarrier::with('participants')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="healthcare_barriers_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['ID', 'District', 'UC Name', 'Session Date', 'Facilitator TKF', 'Facility Name', 'Facility Type', 'Barriers Identified', 'Solutions Proposed', 'Follow Up Actions', 'Participants Count', 'Latitude', 'Longitude', 'Created At'];

        $callback = function () use ($healthcareBarriers, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($healthcareBarriers as $item) {
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
            'Content-Disposition' => 'attachment; filename="healthcare_barriers_template.csv"',
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
                HealthcareBarrier::create([
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

        return redirect()->route('admin.healthcare-barriers.index')
            ->with('success', $message);
    }
}
