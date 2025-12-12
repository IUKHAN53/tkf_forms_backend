<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\AreaMapping;
use App\Models\DraftList;
use App\Models\ReligiousLeader;
use App\Models\CommunityBarrier;
use App\Models\HealthcareBarrier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Core Forms Statistics
        $coreFormsStats = [
            'area_mappings' => AreaMapping::count(),
            'draft_lists' => DraftList::count(),
            'religious_leaders' => ReligiousLeader::count(),
            'community_barriers' => CommunityBarrier::count(),
            'healthcare_barriers' => HealthcareBarrier::count(),
        ];

        // Submissions over time (last 30 days)
        $submissionsOverTime = $this->getSubmissionsOverTime();

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
            'submissions_over_time' => $submissionsOverTime,
            'district_distribution' => $districtDistribution,
            'recent_activity' => $recentActivity,
        ];

        return view('admin.dashboard', compact('stats'));
    }

    private function getSubmissionsOverTime()
    {
        $days = 30;
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $data[$date] = [
                'area_mappings' => AreaMapping::whereDate('created_at', $date)->count(),
                'draft_lists' => DraftList::whereDate('created_at', $date)->count(),
                'religious_leaders' => ReligiousLeader::whereDate('created_at', $date)->count(),
                'community_barriers' => CommunityBarrier::whereDate('created_at', $date)->count(),
                'healthcare_barriers' => HealthcareBarrier::whereDate('created_at', $date)->count(),
            ];
        }

        return $data;
    }

    private function getDistrictDistribution()
    {
        $districts = [];

        // Get districts from area mappings
        $areaMappings = AreaMapping::select('district', DB::raw('count(*) as count'))
            ->groupBy('district')
            ->get();

        foreach ($areaMappings as $item) {
            if (!isset($districts[$item->district])) {
                $districts[$item->district] = 0;
            }
            $districts[$item->district] += $item->count;
        }

        // Get districts from religious leaders
        $religiousLeaders = ReligiousLeader::select('district', DB::raw('count(*) as count'))
            ->groupBy('district')
            ->get();

        foreach ($religiousLeaders as $item) {
            if (!isset($districts[$item->district])) {
                $districts[$item->district] = 0;
            }
            $districts[$item->district] += $item->count;
        }

        // Get districts from community barriers
        $communityBarriers = CommunityBarrier::select('district', DB::raw('count(*) as count'))
            ->groupBy('district')
            ->get();

        foreach ($communityBarriers as $item) {
            if (!isset($districts[$item->district])) {
                $districts[$item->district] = 0;
            }
            $districts[$item->district] += $item->count;
        }

        return $districts;
    }

    private function getRecentActivity()
    {
        $activity = collect();

        // Area Mappings
        AreaMapping::latest()->take(5)->get()->each(function ($item) use ($activity) {
            $activity->push([
                'type' => 'Area Mapping',
                'district' => $item->district,
                'uc' => $item->uc_name,
                'created_at' => $item->created_at,
            ]);
        });

        // Religious Leaders
        ReligiousLeader::latest()->take(5)->get()->each(function ($item) use ($activity) {
            $activity->push([
                'type' => 'Religious Leader',
                'district' => $item->district,
                'uc' => $item->uc,
                'created_at' => $item->created_at,
            ]);
        });

        // Community Barriers
        CommunityBarrier::latest()->take(5)->get()->each(function ($item) use ($activity) {
            $activity->push([
                'type' => 'Community Barrier',
                'district' => $item->district,
                'uc' => $item->uc,
                'created_at' => $item->created_at,
            ]);
        });

        return $activity->sortByDesc('created_at')->take(10);
    }
}
