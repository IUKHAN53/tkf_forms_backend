<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\ChildLineList;
use App\Models\FgdsCommunity;
use App\Models\FgdsHealthWorkers;
use App\Models\BridgingTheGap;
use App\Models\OutreachSite;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Core Forms Statistics
        $coreFormsStats = [
            'child_line_lists' => ChildLineList::count(),
            'fgds_community' => FgdsCommunity::count(),
            'fgds_health_workers' => FgdsHealthWorkers::count(),
            'bridging_the_gap' => BridgingTheGap::count(),
        ];

        // UC-wise submissions (stacked by form type)
        $ucWiseSubmissions = $this->getUcWiseSubmissions();

        // District-wise distribution
        $districtDistribution = $this->getDistrictDistribution();

        // Recent activity
        $recentActivity = $this->getRecentActivity();

        $stats = [
            'total_forms' => Form::count(),
            'active_forms' => Form::where('is_active', true)->count(),
            'total_submissions' => FormSubmission::count(),
            'recent_submissions' => FormSubmission::with('form', 'user')->latest()->take(10)->get(),
            'core_forms' => $coreFormsStats,
            'uc_wise_submissions' => $ucWiseSubmissions,
            'district_distribution' => $districtDistribution,
            'recent_activity' => $recentActivity,
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Get UC-wise submissions for stacked graph
     */
    private function getUcWiseSubmissions(?string $startDate = null, ?string $endDate = null)
    {
        $data = [];

        // Get ALL union councils from OutreachSite and initialize with 0 counts
        $allUcs = OutreachSite::distinct()
            ->whereNotNull('union_council')
            ->where('union_council', '!=', '')
            ->orderBy('union_council')
            ->pluck('union_council');

        foreach ($allUcs as $uc) {
            $data[$uc] = [
                'child_line_lists' => 0,
                'fgds_community' => 0,
                'fgds_health_workers' => 0,
                'bridging_the_gap' => 0,
            ];
        }

        // Child Line Lists by UC
        $query = ChildLineList::select('uc', DB::raw('count(*) as count'))
            ->whereNotNull('uc')
            ->where('uc', '!=', '');
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        $childLineLists = $query->groupBy('uc')->get();

        foreach ($childLineLists as $item) {
            if (isset($data[$item->uc])) {
                $data[$item->uc]['child_line_lists'] = $item->count;
            }
        }

        // FGDs-Community by UC
        $query = FgdsCommunity::select('uc', DB::raw('count(*) as count'))
            ->whereNotNull('uc')
            ->where('uc', '!=', '');
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        $fgdsCommunity = $query->groupBy('uc')->get();

        foreach ($fgdsCommunity as $item) {
            if (isset($data[$item->uc])) {
                $data[$item->uc]['fgds_community'] = $item->count;
            }
        }

        // FGDs-Health Workers by UC
        $query = FgdsHealthWorkers::select('uc', DB::raw('count(*) as count'))
            ->whereNotNull('uc')
            ->where('uc', '!=', '');
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        $fgdsHealthWorkers = $query->groupBy('uc')->get();

        foreach ($fgdsHealthWorkers as $item) {
            if (isset($data[$item->uc])) {
                $data[$item->uc]['fgds_health_workers'] = $item->count;
            }
        }

        // Bridging The Gap by UC
        $query = BridgingTheGap::select('uc', DB::raw('count(*) as count'))
            ->whereNotNull('uc')
            ->where('uc', '!=', '');
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        $bridgingTheGap = $query->groupBy('uc')->get();

        foreach ($bridgingTheGap as $item) {
            if (isset($data[$item->uc])) {
                $data[$item->uc]['bridging_the_gap'] = $item->count;
            }
        }

        // Sort by total count descending (UCs with data first, then alphabetically for 0s)
        uasort($data, function ($a, $b) {
            $totalA = array_sum($a);
            $totalB = array_sum($b);
            return $totalB <=> $totalA;
        });

        return $data;
    }

    /**
     * Get district-wise distribution for chart
     */
    private function getDistrictDistribution(?string $startDate = null, ?string $endDate = null)
    {
        $districts = [];

        // Get ALL districts from OutreachSite and initialize with 0 counts
        $allDistricts = OutreachSite::distinct()
            ->whereNotNull('district')
            ->where('district', '!=', '')
            ->orderBy('district')
            ->pluck('district');

        foreach ($allDistricts as $district) {
            $districts[$district] = [
                'child_line_lists' => 0,
                'fgds_community' => 0,
                'fgds_health_workers' => 0,
                'bridging_the_gap' => 0,
            ];
        }

        // Child Line Lists by district
        $query = ChildLineList::select('district', DB::raw('count(*) as count'))
            ->whereNotNull('district')
            ->where('district', '!=', '');
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        $childLineLists = $query->groupBy('district')->get();

        foreach ($childLineLists as $item) {
            if (isset($districts[$item->district])) {
                $districts[$item->district]['child_line_lists'] = $item->count;
            }
        }

        // FGDs-Community by district
        $query = FgdsCommunity::select('district', DB::raw('count(*) as count'))
            ->whereNotNull('district')
            ->where('district', '!=', '');
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        $fgdsCommunity = $query->groupBy('district')->get();

        foreach ($fgdsCommunity as $item) {
            if (isset($districts[$item->district])) {
                $districts[$item->district]['fgds_community'] = $item->count;
            }
        }

        // FGDs-Health Workers - doesn't have district directly, skip for now

        // Bridging The Gap by district
        $query = BridgingTheGap::select('district', DB::raw('count(*) as count'))
            ->whereNotNull('district')
            ->where('district', '!=', '');
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        $bridgingTheGap = $query->groupBy('district')->get();

        foreach ($bridgingTheGap as $item) {
            if (isset($districts[$item->district])) {
                $districts[$item->district]['bridging_the_gap'] = $item->count;
            }
        }

        // Sort by total count descending
        uasort($districts, function ($a, $b) {
            $totalA = array_sum($a);
            $totalB = array_sum($b);
            return $totalB <=> $totalA;
        });

        return $districts;
    }

    /**
     * Get recent activity across all forms
     */
    private function getRecentActivity()
    {
        $activity = collect();

        // Child Line Lists
        ChildLineList::latest()->take(5)->get()->each(function ($item) use ($activity) {
            $activity->push([
                'type' => 'Child Line List',
                'description' => $item->child_name . ' - ' . $item->type,
                'district' => $item->district,
                'uc' => $item->uc,
                'created_at' => $item->created_at,
            ]);
        });

        // FGDs-Community
        FgdsCommunity::latest()->take(5)->get()->each(function ($item) use ($activity) {
            $activity->push([
                'type' => 'FGDs-Community',
                'description' => $item->venue,
                'district' => $item->district,
                'uc' => $item->uc,
                'created_at' => $item->created_at,
            ]);
        });

        // FGDs-Health Workers
        FgdsHealthWorkers::latest()->take(5)->get()->each(function ($item) use ($activity) {
            $activity->push([
                'type' => 'FGDs-Health Workers',
                'description' => $item->hfs,
                'district' => null,
                'uc' => $item->uc,
                'created_at' => $item->created_at,
            ]);
        });

        // Bridging The Gap
        BridgingTheGap::latest()->take(5)->get()->each(function ($item) use ($activity) {
            $activity->push([
                'type' => 'Bridging The Gap',
                'description' => $item->venue,
                'district' => $item->district,
                'uc' => $item->uc,
                'created_at' => $item->created_at,
            ]);
        });

        return $activity->sortByDesc('created_at')->take(10);
    }

    /**
     * Get chart data via AJAX with date filtering
     */
    public function chartData(Request $request): JsonResponse
    {
        $chart = $request->get('chart', 'uc');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Validate dates if provided
        if ($startDate) {
            try {
                $startDate = Carbon::parse($startDate)->format('Y-m-d');
            } catch (\Exception $e) {
                $startDate = null;
            }
        }

        if ($endDate) {
            try {
                $endDate = Carbon::parse($endDate)->format('Y-m-d');
            } catch (\Exception $e) {
                $endDate = null;
            }
        }

        if ($chart === 'district') {
            $data = $this->getDistrictDistribution($startDate, $endDate);
        } else {
            $data = $this->getUcWiseSubmissions($startDate, $endDate);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }
}
