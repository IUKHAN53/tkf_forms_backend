<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\ChildLineList;
use App\Models\FgdsCommunity;
use App\Models\FgdsHealthWorkers;
use App\Models\BridgingTheGap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    private function getUcWiseSubmissions()
    {
        $data = [];

        // Child Line Lists by UC
        $childLineLists = ChildLineList::select('uc', DB::raw('count(*) as count'))
            ->whereNotNull('uc')
            ->where('uc', '!=', '')
            ->groupBy('uc')
            ->get();

        foreach ($childLineLists as $item) {
            if (!isset($data[$item->uc])) {
                $data[$item->uc] = [
                    'child_line_lists' => 0,
                    'fgds_community' => 0,
                    'fgds_health_workers' => 0,
                    'bridging_the_gap' => 0,
                ];
            }
            $data[$item->uc]['child_line_lists'] = $item->count;
        }

        // FGDs-Community by UC
        $fgdsCommunity = FgdsCommunity::select('uc', DB::raw('count(*) as count'))
            ->whereNotNull('uc')
            ->where('uc', '!=', '')
            ->groupBy('uc')
            ->get();

        foreach ($fgdsCommunity as $item) {
            if (!isset($data[$item->uc])) {
                $data[$item->uc] = [
                    'child_line_lists' => 0,
                    'fgds_community' => 0,
                    'fgds_health_workers' => 0,
                    'bridging_the_gap' => 0,
                ];
            }
            $data[$item->uc]['fgds_community'] = $item->count;
        }

        // FGDs-Health Workers by UC
        $fgdsHealthWorkers = FgdsHealthWorkers::select('uc', DB::raw('count(*) as count'))
            ->whereNotNull('uc')
            ->where('uc', '!=', '')
            ->groupBy('uc')
            ->get();

        foreach ($fgdsHealthWorkers as $item) {
            if (!isset($data[$item->uc])) {
                $data[$item->uc] = [
                    'child_line_lists' => 0,
                    'fgds_community' => 0,
                    'fgds_health_workers' => 0,
                    'bridging_the_gap' => 0,
                ];
            }
            $data[$item->uc]['fgds_health_workers'] = $item->count;
        }

        // Bridging The Gap by UC
        $bridgingTheGap = BridgingTheGap::select('uc', DB::raw('count(*) as count'))
            ->whereNotNull('uc')
            ->where('uc', '!=', '')
            ->groupBy('uc')
            ->get();

        foreach ($bridgingTheGap as $item) {
            if (!isset($data[$item->uc])) {
                $data[$item->uc] = [
                    'child_line_lists' => 0,
                    'fgds_community' => 0,
                    'fgds_health_workers' => 0,
                    'bridging_the_gap' => 0,
                ];
            }
            $data[$item->uc]['bridging_the_gap'] = $item->count;
        }

        // Sort by total count descending and take top 15
        uasort($data, function ($a, $b) {
            $totalA = array_sum($a);
            $totalB = array_sum($b);
            return $totalB <=> $totalA;
        });

        return array_slice($data, 0, 15, true);
    }

    /**
     * Get district-wise distribution for chart
     */
    private function getDistrictDistribution()
    {
        $districts = [];

        // Child Line Lists by district
        $childLineLists = ChildLineList::select('district', DB::raw('count(*) as count'))
            ->whereNotNull('district')
            ->where('district', '!=', '')
            ->groupBy('district')
            ->get();

        foreach ($childLineLists as $item) {
            if (!isset($districts[$item->district])) {
                $districts[$item->district] = [
                    'child_line_lists' => 0,
                    'fgds_community' => 0,
                    'fgds_health_workers' => 0,
                    'bridging_the_gap' => 0,
                ];
            }
            $districts[$item->district]['child_line_lists'] = $item->count;
        }

        // FGDs-Community by district
        $fgdsCommunity = FgdsCommunity::select('district', DB::raw('count(*) as count'))
            ->whereNotNull('district')
            ->where('district', '!=', '')
            ->groupBy('district')
            ->get();

        foreach ($fgdsCommunity as $item) {
            if (!isset($districts[$item->district])) {
                $districts[$item->district] = [
                    'child_line_lists' => 0,
                    'fgds_community' => 0,
                    'fgds_health_workers' => 0,
                    'bridging_the_gap' => 0,
                ];
            }
            $districts[$item->district]['fgds_community'] = $item->count;
        }

        // FGDs-Health Workers - doesn't have district directly, skip for now

        // Bridging The Gap by district
        $bridgingTheGap = BridgingTheGap::select('district', DB::raw('count(*) as count'))
            ->whereNotNull('district')
            ->where('district', '!=', '')
            ->groupBy('district')
            ->get();

        foreach ($bridgingTheGap as $item) {
            if (!isset($districts[$item->district])) {
                $districts[$item->district] = [
                    'child_line_lists' => 0,
                    'fgds_community' => 0,
                    'fgds_health_workers' => 0,
                    'bridging_the_gap' => 0,
                ];
            }
            $districts[$item->district]['bridging_the_gap'] = $item->count;
        }

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
}
