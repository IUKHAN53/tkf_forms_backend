<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OutreachSite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OutreachSiteController extends Controller
{
    /**
     * Get all outreach sites with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = OutreachSite::query();

        if ($request->has('district')) {
            $query->where('district', $request->district);
        }

        if ($request->has('union_council')) {
            $query->where('union_council', $request->union_council);
        }

        if ($request->has('fix_site')) {
            $query->where('fix_site', $request->fix_site);
        }

        return response()->json($query->get());
    }

    /**
     * Get distinct districts
     */
    public function districts(): JsonResponse
    {
        $districts = OutreachSite::distinct()
            ->orderBy('district')
            ->pluck('district');

        return response()->json($districts);
    }

    /**
     * Get union councils (towns) for a district
     */
    public function unionCouncils(Request $request): JsonResponse
    {
        $query = OutreachSite::distinct();

        if ($request->has('district')) {
            $query->where('district', $request->district);
        }

        $ucs = $query->orderBy('union_council')->pluck('union_council');

        return response()->json($ucs);
    }

    /**
     * Get fix sites for a district/UC
     */
    public function fixSites(Request $request): JsonResponse
    {
        $query = OutreachSite::distinct();

        if ($request->has('district')) {
            $query->where('district', $request->district);
        }

        if ($request->has('union_council')) {
            $query->where('union_council', $request->union_council);
        }

        $sites = $query->orderBy('fix_site')->pluck('fix_site');

        return response()->json($sites);
    }

    /**
     * Get outreach sites for a fix site
     */
    public function outreachSites(Request $request): JsonResponse
    {
        $query = OutreachSite::query();

        if ($request->has('district')) {
            $query->where('district', $request->district);
        }

        if ($request->has('union_council')) {
            $query->where('union_council', $request->union_council);
        }

        if ($request->has('fix_site')) {
            $query->where('fix_site', $request->fix_site);
        }

        $sites = $query->orderBy('outreach_site')
            ->get(['id', 'outreach_site', 'coordinates']);

        return response()->json($sites);
    }

    /**
     * Store a new outreach site
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'district' => 'required|string',
            'union_council' => 'required|string',
            'fix_site' => 'required|string',
            'outreach_site' => 'required|string',
            'coordinates' => 'nullable|string',
            'comments' => 'nullable|string',
        ]);

        $site = OutreachSite::create($validated);

        return response()->json([
            'message' => 'Outreach site created successfully',
            'data' => $site,
        ], 201);
    }
}
