<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OutreachSite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class OutreachSiteController extends Controller
{
    // Karachi geographic boundaries
    private const KARACHI_LAT_MIN = 24.7;
    private const KARACHI_LAT_MAX = 25.2;
    private const KARACHI_LNG_MIN = 66.9;
    private const KARACHI_LNG_MAX = 67.5;

    public function index(Request $request)
    {
        $query = OutreachSite::query()->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('district', 'like', "%{$search}%")
                    ->orWhere('union_council', 'like', "%{$search}%")
                    ->orWhere('fix_site', 'like', "%{$search}%")
                    ->orWhere('outreach_site', 'like', "%{$search}%");
            });
        }

        // Filter for invalid coordinates only
        if ($request->filled('invalid_coords') && $request->invalid_coords === '1') {
            $query->where(function ($q) {
                $q->whereNotNull('coordinates')
                    ->where('coordinates', '!=', '');
            });
        }

        $outreachSites = $query->paginate(15)->withQueryString();

        // Add coordinate validation status to each site
        $outreachSites->getCollection()->transform(function ($site) {
            $site->coordinate_status = $this->validateCoordinatesInKarachi($site->coordinates);
            return $site;
        });

        // Prepare map data for heatmap visualization
        $mapData = OutreachSite::whereNotNull('coordinates')
            ->where('coordinates', '!=', '')
            ->get()
            ->map(function ($site) {
                // Parse coordinates (format: "lat,lng" or "lat, lng")
                $coords = $this->parseCoordinates($site->coordinates);
                if (!$coords) {
                    return null;
                }
                return [
                    'lat' => $coords['lat'],
                    'lon' => $coords['lng'],
                    'popup' => "<strong>{$site->outreach_site}</strong><br>
                               District: {$site->district}<br>
                               UC: {$site->union_council}<br>
                               Fix Site: {$site->fix_site}",
                ];
            })
            ->filter()
            ->values()
            ->toArray();

        return view('admin.outreach-sites.index', compact('outreachSites', 'mapData'));
    }

    /**
     * Parse coordinates from string format
     */
    private function parseCoordinates(?string $coordinates): ?array
    {
        if (empty($coordinates)) {
            return null;
        }

        // Try different formats: "lat,lng", "lat, lng", "(lat, lng)"
        $coordinates = trim($coordinates, '() ');
        $parts = preg_split('/[,\s]+/', $coordinates, -1, PREG_SPLIT_NO_EMPTY);

        if (count($parts) >= 2) {
            $lat = (float) $parts[0];
            $lng = (float) $parts[1];

            // Validate coordinates are in reasonable range
            if ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                return ['lat' => $lat, 'lng' => $lng];
            }
        }

        return null;
    }

    /**
     * Validate if coordinates fall within Karachi zone
     * Returns: 'valid', 'invalid', or 'missing'
     */
    private function validateCoordinatesInKarachi(?string $coordinates): string
    {
        if (empty($coordinates)) {
            return 'missing';
        }

        $coords = $this->parseCoordinates($coordinates);

        if (!$coords) {
            return 'invalid';
        }

        $lat = $coords['lat'];
        $lng = $coords['lng'];

        // Check if within Karachi boundaries
        if ($lat >= self::KARACHI_LAT_MIN && $lat <= self::KARACHI_LAT_MAX &&
            $lng >= self::KARACHI_LNG_MIN && $lng <= self::KARACHI_LNG_MAX) {
            return 'valid';
        }

        return 'outside_karachi';
    }

    public function create()
    {
        return view('admin.outreach-sites.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'district' => 'required|string|max:255',
            'union_council' => 'required|string|max:255',
            'fix_site' => 'required|string|max:255',
            'outreach_site' => 'required|string|max:255',
            'coordinates' => 'nullable|string|max:255',
            'comments' => 'nullable|string',
        ]);

        OutreachSite::create($validated);

        return redirect()->route('admin.outreach-sites.index')
            ->with('success', 'Vaccination site created successfully');
    }

    public function edit(OutreachSite $outreachSite)
    {
        return view('admin.outreach-sites.edit', compact('outreachSite'));
    }

    public function update(Request $request, OutreachSite $outreachSite)
    {
        $validated = $request->validate([
            'district' => 'required|string|max:255',
            'union_council' => 'required|string|max:255',
            'fix_site' => 'required|string|max:255',
            'outreach_site' => 'required|string|max:255',
            'coordinates' => 'nullable|string|max:255',
            'comments' => 'nullable|string',
        ]);

        $outreachSite->update($validated);

        return redirect()->route('admin.outreach-sites.index')
            ->with('success', 'Vaccination site updated successfully');
    }

    public function destroy(OutreachSite $outreachSite)
    {
        $outreachSite->delete();
        return redirect()->route('admin.outreach-sites.index')
            ->with('success', 'Vaccination site deleted successfully');
    }

    public function export()
    {
        $outreachSites = OutreachSite::all();

        $columns = ['ID', 'District', 'Union Council', 'Fix Site', 'Outreach Site', 'Coordinates', 'Comments', 'Created At'];

        $callback = function () use ($outreachSites, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($outreachSites as $site) {
                fputcsv($file, [
                    $site->id,
                    $site->district,
                    $site->union_council,
                    $site->fix_site,
                    $site->outreach_site,
                    $site->coordinates,
                    $site->comments,
                    $site->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="outreach_sites_' . date('Y-m-d_His') . '.csv"',
        ]);
    }

    public function template()
    {
        $columns = ['district', 'union_council', 'fix_site', 'outreach_site', 'coordinates', 'comments'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fclose($file);
        };

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="outreach_sites_template.csv"',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $csv = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($csv);

        $imported = 0;
        foreach ($csv as $row) {
            if (count($row) < 4) continue;

            $data = array_combine($header, $row);
            OutreachSite::create([
                'district' => $data['district'] ?? '',
                'union_council' => $data['union_council'] ?? '',
                'fix_site' => $data['fix_site'] ?? '',
                'outreach_site' => $data['outreach_site'] ?? '',
                'coordinates' => $data['coordinates'] ?? null,
                'comments' => $data['comments'] ?? null,
            ]);
            $imported++;
        }

        return redirect()->route('admin.outreach-sites.index')
            ->with('success', "Successfully imported {$imported} vaccination sites");
    }
}
