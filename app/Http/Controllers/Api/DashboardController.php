<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AreaMapping;
use App\Models\DraftList;
use App\Models\ReligiousLeader;
use App\Models\CommunityBarrier;
use App\Models\HealthcareBarrier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics for the authenticated user
     */
    public function stats(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        // Get weekly submissions for each form type (last 7 days)
        $weeklyData = $this->getWeeklySubmissions($userId);

        // Get recent activity
        $recentActivity = $this->getRecentActivity($userId);

        // Get totals for the user
        $totals = [
            'area_mappings' => AreaMapping::where('user_id', $userId)->count(),
            'draft_lists' => DraftList::where('user_id', $userId)->count(),
            'religious_leaders' => ReligiousLeader::where('user_id', $userId)->count(),
            'community_barriers' => CommunityBarrier::where('user_id', $userId)->count(),
            'healthcare_barriers' => HealthcareBarrier::where('user_id', $userId)->count(),
        ];

        // Calculate today's submissions and comparison
        $todayCount = $this->getTodayCount($userId);
        $yesterdayCount = $this->getYesterdayCount($userId);
        $percentChange = $yesterdayCount > 0 
            ? round((($todayCount - $yesterdayCount) / $yesterdayCount) * 100) 
            : ($todayCount > 0 ? 100 : 0);

        return response()->json([
            'weekly_data' => $weeklyData,
            'recent_activity' => $recentActivity,
            'totals' => $totals,
            'today' => [
                'count' => $todayCount,
                'percent_change' => $percentChange,
            ],
            'this_week' => array_sum($weeklyData),
        ]);
    }

    /**
     * Get weekly submissions count for the last 7 days
     */
    private function getWeeklySubmissions(int $userId): array
    {
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            
            $count = 0;
            $count += AreaMapping::where('user_id', $userId)->whereDate('created_at', $date)->count();
            $count += DraftList::where('user_id', $userId)->whereDate('created_at', $date)->count();
            $count += ReligiousLeader::where('user_id', $userId)->whereDate('created_at', $date)->count();
            $count += CommunityBarrier::where('user_id', $userId)->whereDate('created_at', $date)->count();
            $count += HealthcareBarrier::where('user_id', $userId)->whereDate('created_at', $date)->count();

            $data[] = $count;
        }

        return $data;
    }

    /**
     * Get recent activity for the user
     */
    private function getRecentActivity(int $userId): array
    {
        $activities = collect();

        // Get recent from each model
        $areaMappings = AreaMapping::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'type' => 'area_mapping',
                'action' => 'Submitted Area Mapping',
                'icon' => '🗺️',
                'time' => $item->created_at,
            ]);

        $draftLists = DraftList::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'type' => 'draft_list',
                'action' => 'Submitted Draft List - ' . $item->child_name,
                'icon' => '📋',
                'time' => $item->created_at,
            ]);

        $religiousLeaders = ReligiousLeader::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'type' => 'religious_leader',
                'action' => 'Submitted Religious Leaders Session',
                'icon' => '🕌',
                'time' => $item->created_at,
            ]);

        $communityBarriers = CommunityBarrier::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'type' => 'community_barrier',
                'action' => 'Submitted Community Barrier Analysis',
                'icon' => '👥',
                'time' => $item->created_at,
            ]);

        $healthcareBarriers = HealthcareBarrier::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'type' => 'healthcare_barrier',
                'action' => 'Submitted Healthcare Barrier Analysis',
                'icon' => '🏥',
                'time' => $item->created_at,
            ]);

        // Merge and sort by time
        $activities = $activities
            ->merge($areaMappings)
            ->merge($draftLists)
            ->merge($religiousLeaders)
            ->merge($communityBarriers)
            ->merge($healthcareBarriers)
            ->sortByDesc('time')
            ->take(10)
            ->values()
            ->map(function ($item) {
                return [
                    'type' => $item['type'],
                    'action' => $item['action'],
                    'icon' => $item['icon'],
                    'time' => $item['time']->diffForHumans(),
                ];
            });

        return $activities->toArray();
    }

    /**
     * Get today's submission count
     */
    private function getTodayCount(int $userId): int
    {
        $today = now()->format('Y-m-d');
        
        $count = 0;
        $count += AreaMapping::where('user_id', $userId)->whereDate('created_at', $today)->count();
        $count += DraftList::where('user_id', $userId)->whereDate('created_at', $today)->count();
        $count += ReligiousLeader::where('user_id', $userId)->whereDate('created_at', $today)->count();
        $count += CommunityBarrier::where('user_id', $userId)->whereDate('created_at', $today)->count();
        $count += HealthcareBarrier::where('user_id', $userId)->whereDate('created_at', $today)->count();

        return $count;
    }

    /**
     * Get yesterday's submission count
     */
    private function getYesterdayCount(int $userId): int
    {
        $yesterday = now()->subDay()->format('Y-m-d');
        
        $count = 0;
        $count += AreaMapping::where('user_id', $userId)->whereDate('created_at', $yesterday)->count();
        $count += DraftList::where('user_id', $userId)->whereDate('created_at', $yesterday)->count();
        $count += ReligiousLeader::where('user_id', $userId)->whereDate('created_at', $yesterday)->count();
        $count += CommunityBarrier::where('user_id', $userId)->whereDate('created_at', $yesterday)->count();
        $count += HealthcareBarrier::where('user_id', $userId)->whereDate('created_at', $yesterday)->count();

        return $count;
    }
}
