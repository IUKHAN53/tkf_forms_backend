<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReligiousLeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReligiousLeaderController extends Controller
{
    public function index(Request $request)
    {
        $query = ReligiousLeader::with('participants')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('district', 'like', "%{$search}%")
                    ->orWhere('uc_name', 'like', "%{$search}%")
                    ->orWhere('facilitator_tkf', 'like', "%{$search}%")
                    ->orWhere('venue', 'like', "%{$search}%");
            });
        }

        $religiousLeaders = $query->paginate(15)->withQueryString();

        return view('admin.core-forms.religious-leaders.index', compact('religiousLeaders'));
    }

    public function show(ReligiousLeader $religiousLeader)
    {
        $religiousLeader->load('participants');
        return view('admin.core-forms.religious-leaders.show', compact('religiousLeader'));
    }

    public function destroy(ReligiousLeader $religiousLeader)
    {
        $religiousLeader->participants()->delete();
        $religiousLeader->delete();
        return redirect()->route('admin.religious-leaders.index')
            ->with('success', 'Religious leader session deleted successfully.');
    }

    public function export()
    {
        $religiousLeaders = ReligiousLeader::with('participants')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="religious_leaders_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['ID', 'District', 'UC Name', 'Session Date', 'Facilitator TKF', 'Venue', 'Topics Discussed', 'Outcomes', 'Participants Count', 'Latitude', 'Longitude', 'Created At'];

        $callback = function () use ($religiousLeaders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($religiousLeaders as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->district,
                    $item->uc_name,
                    $item->session_date,
                    $item->facilitator_tkf,
                    $item->venue,
                    $item->topics_discussed,
                    $item->outcomes,
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
            'Content-Disposition' => 'attachment; filename="religious_leaders_template.csv"',
        ];

        $columns = ['district', 'uc_name', 'session_date', 'facilitator_tkf', 'venue', 'topics_discussed', 'outcomes', 'latitude', 'longitude'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['District Name', 'UC Name', '2025-01-15', 'Facilitator Name', 'Mosque/Venue', 'Vaccination importance', 'Community support', '31.5204', '74.3587']);
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
                ReligiousLeader::create([
                    'district' => $row[0],
                    'uc_name' => $row[1],
                    'session_date' => $row[2],
                    'facilitator_tkf' => $row[3],
                    'venue' => $row[4],
                    'topics_discussed' => $row[5],
                    'outcomes' => $row[6],
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

        return redirect()->route('admin.religious-leaders.index')
            ->with('success', $message);
    }
}
