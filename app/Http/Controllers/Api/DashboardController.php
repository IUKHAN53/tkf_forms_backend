<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChildLineList;
use App\Models\FgdsCommunity;
use App\Models\FgdsHealthWorkers;
use App\Models\BridgingTheGap;
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

        // Get totals for the user (now showing children count for Child Line List)
        $totals = [
            'child_line_lists' => ChildLineList::where('user_id', $userId)->count(),
            'fgds_community' => FgdsCommunity::where('user_id', $userId)->count(),
            'fgds_health_workers' => FgdsHealthWorkers::where('user_id', $userId)->count(),
            'bridging_the_gap' => BridgingTheGap::where('user_id', $userId)->count(),
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
            $count += ChildLineList::where('user_id', $userId)->whereDate('created_at', $date)->count();
            $count += FgdsCommunity::where('user_id', $userId)->whereDate('created_at', $date)->count();
            $count += FgdsHealthWorkers::where('user_id', $userId)->whereDate('created_at', $date)->count();
            $count += BridgingTheGap::where('user_id', $userId)->whereDate('created_at', $date)->count();

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
        $childLineLists = ChildLineList::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'type' => 'child_line_list',
                'action' => 'Recorded Child - ' . $item->child_name,
                'icon' => 'baby',
                'time' => $item->created_at,
            ]);

        $fgdsCommunity = FgdsCommunity::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'type' => 'fgds_community',
                'action' => 'FGDs-Community Session',
                'icon' => 'users',
                'time' => $item->created_at,
            ]);

        $fgdsHealthWorkers = FgdsHealthWorkers::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'type' => 'fgds_health_workers',
                'action' => 'FGDs-Health Workers Session',
                'icon' => 'hospital',
                'time' => $item->created_at,
            ]);

        $bridgingTheGap = BridgingTheGap::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'type' => 'bridging_the_gap',
                'action' => 'Bridging The Gap Session',
                'icon' => 'bridge',
                'time' => $item->created_at,
            ]);

        // Merge and sort by time
        $activities = $activities
            ->merge($childLineLists)
            ->merge($fgdsCommunity)
            ->merge($fgdsHealthWorkers)
            ->merge($bridgingTheGap)
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
        $count += ChildLineList::where('user_id', $userId)->whereDate('created_at', $today)->count();
        $count += FgdsCommunity::where('user_id', $userId)->whereDate('created_at', $today)->count();
        $count += FgdsHealthWorkers::where('user_id', $userId)->whereDate('created_at', $today)->count();
        $count += BridgingTheGap::where('user_id', $userId)->whereDate('created_at', $today)->count();

        return $count;
    }

    /**
     * Get yesterday's submission count
     */
    private function getYesterdayCount(int $userId): int
    {
        $yesterday = now()->subDay()->format('Y-m-d');

        $count = 0;
        $count += ChildLineList::where('user_id', $userId)->whereDate('created_at', $yesterday)->count();
        $count += FgdsCommunity::where('user_id', $userId)->whereDate('created_at', $yesterday)->count();
        $count += FgdsHealthWorkers::where('user_id', $userId)->whereDate('created_at', $yesterday)->count();
        $count += BridgingTheGap::where('user_id', $userId)->whereDate('created_at', $yesterday)->count();

        return $count;
    }
}
