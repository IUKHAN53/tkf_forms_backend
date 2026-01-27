<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChildLineList;
use App\Models\FgdsCommunity;
use App\Models\FgdsHealthWorkers;
use App\Models\BridgingTheGap;
use App\Models\FgdsCommunityBarrier;
use App\Models\BridgingTheGapActionPlan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UcController extends Controller
{
    /**
     * Display UC detail page with tabs
     */
    public function show(string $slug)
    {
        // Convert slug back to UC name
        $ucName = $this->getUcNameFromSlug($slug);

        if (!$ucName) {
            abort(404, 'UC not found');
        }

        // Get UC variants for querying
        $variants = DashboardController::getUcVariants($ucName);

        // Get overall stats for the UC
        $stats = $this->getUcOverallStats($variants);

        return view('admin.uc.show', [
            'ucName' => $ucName,
            'ucSlug' => $slug,
            'stats' => $stats,
            'variants' => $variants,
        ]);
    }

    /**
     * Get UC data via AJAX for tabs
     */
    public function getData(Request $request, string $slug): JsonResponse
    {
        $ucName = $this->getUcNameFromSlug($slug);

        if (!$ucName) {
            return response()->json(['error' => 'UC not found'], 404);
        }

        $variants = DashboardController::getUcVariants($ucName);
        $tab = $request->get('tab', 'fgds_community');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Parse dates
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

        $data = [];

        switch ($tab) {
            case 'fgds_community':
                $data = $this->getFgdsCommunityData($variants, $startDate, $endDate);
                break;
            case 'fgds_health_workers':
                $data = $this->getFgdsHealthWorkersData($variants, $startDate, $endDate);
                break;
            case 'bridging_the_gap':
                $data = $this->getBridgingTheGapData($variants, $startDate, $endDate);
                break;
            case 'child_line_list':
                $data = $this->getChildLineListData($variants, $startDate, $endDate);
                break;
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'tab' => $tab,
            ],
        ]);
    }

    /**
     * Convert slug to UC name
     */
    private function getUcNameFromSlug(string $slug): ?string
    {
        $ucMapping = [
            'muslimabad' => 'Muslimabad',
            'muzafrabad' => 'Muzafrabad',
            'gujro' => 'Gujro',
            'songal' => 'Songal',
            'ittehad-town-2' => 'Ittehad Town-2',
            'islamia-colony-09' => 'Islamia Colony-09',
            'chishti-nagar-7' => 'Chishti Nagar-7',
            'uc-8-manghopir' => 'UC 8 Manghopir',
            'zone-e' => 'Zone E',
        ];

        return $ucMapping[$slug] ?? null;
    }

    /**
     * Get overall stats for UC
     */
    private function getUcOverallStats(array $variants): array
    {
        return [
            'child_line_lists' => ChildLineList::where(function ($q) use ($variants) {
                $q->whereIn('uc', $variants);
                foreach ($variants as $variant) {
                    $q->orWhere('uc', 'LIKE', $variant);
                }
            })->count(),
            'fgds_community' => FgdsCommunity::where(function ($q) use ($variants) {
                $q->whereIn('uc', $variants);
                foreach ($variants as $variant) {
                    $q->orWhere('uc', 'LIKE', $variant);
                }
            })->count(),
            'fgds_health_workers' => FgdsHealthWorkers::where(function ($q) use ($variants) {
                $q->whereIn('uc', $variants);
                foreach ($variants as $variant) {
                    $q->orWhere('uc', 'LIKE', $variant);
                }
            })->count(),
            'bridging_the_gap' => BridgingTheGap::where(function ($q) use ($variants) {
                $q->whereIn('uc', $variants);
                foreach ($variants as $variant) {
                    $q->orWhere('uc', 'LIKE', $variant);
                }
            })->count(),
        ];
    }

    /**
     * Get FGDs Community data for UC
     */
    private function getFgdsCommunityData(array $variants, ?string $startDate, ?string $endDate): array
    {
        $query = FgdsCommunity::with(['participants', 'barriers.category', 'user'])
            ->where(function ($q) use ($variants) {
                $q->whereIn('uc', $variants);
                foreach ($variants as $variant) {
                    $q->orWhere('uc', 'LIKE', $variant);
                }
            });

        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }

        $records = $query->latest()->get();

        // Calculate stats
        $totalMales = $records->sum('participants_males');
        $totalFemales = $records->sum('participants_females');
        $totalParticipants = $totalMales + $totalFemales;

        // Get barriers count
        $recordIds = $records->pluck('id');
        $totalBarriers = FgdsCommunityBarrier::whereIn('fgds_community_id', $recordIds)->count();

        // Recent submissions (last 7 days)
        $recentCount = FgdsCommunity::where(function ($q) use ($variants) {
                $q->whereIn('uc', $variants);
                foreach ($variants as $variant) {
                    $q->orWhere('uc', 'LIKE', $variant);
                }
            })
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        return [
            'stats' => [
                'total' => $records->count(),
                'total_males' => $totalMales,
                'total_females' => $totalFemales,
                'total_participants' => $totalParticipants,
                'total_barriers' => $totalBarriers,
                'recent' => $recentCount,
            ],
            'records' => $records->map(function ($item) {
                return [
                    'id' => $item->id,
                    'unique_id' => $item->unique_id,
                    'date' => $item->date ? $item->date->format('M d, Y') : 'N/A',
                    'venue' => $item->venue,
                    'district' => $item->district,
                    'uc' => $item->uc,
                    'participants_males' => $item->participants_males,
                    'participants_females' => $item->participants_females,
                    'total_participants' => $item->participants_males + $item->participants_females,
                    'barriers_count' => $item->barriers->count(),
                    'facilitator' => $item->facilitator_tkf,
                    'submitted_by' => $item->user->name ?? 'N/A',
                    'created_at' => $item->created_at->format('M d, Y'),
                ];
            }),
        ];
    }

    /**
     * Get FGDs Health Workers data for UC
     */
    private function getFgdsHealthWorkersData(array $variants, ?string $startDate, ?string $endDate): array
    {
        $query = FgdsHealthWorkers::with(['participants', 'user'])
            ->where(function ($q) use ($variants) {
                $q->whereIn('uc', $variants);
                foreach ($variants as $variant) {
                    $q->orWhere('uc', 'LIKE', $variant);
                }
            });

        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }

        $records = $query->latest()->get();

        // Calculate stats
        $totalMales = $records->sum('participants_males');
        $totalFemales = $records->sum('participants_females');
        $totalParticipants = $totalMales + $totalFemales;

        // Recent submissions (last 7 days)
        $recentCount = FgdsHealthWorkers::where(function ($q) use ($variants) {
                $q->whereIn('uc', $variants);
                foreach ($variants as $variant) {
                    $q->orWhere('uc', 'LIKE', $variant);
                }
            })
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        return [
            'stats' => [
                'total' => $records->count(),
                'total_males' => $totalMales,
                'total_females' => $totalFemales,
                'total_participants' => $totalParticipants,
                'recent' => $recentCount,
            ],
            'records' => $records->map(function ($item) {
                return [
                    'id' => $item->id,
                    'unique_id' => $item->unique_id,
                    'date' => $item->date ? $item->date->format('M d, Y') : 'N/A',
                    'hfs' => $item->hfs,
                    'uc' => $item->uc,
                    'participants_males' => $item->participants_males,
                    'participants_females' => $item->participants_females,
                    'total_participants' => $item->participants_males + $item->participants_females,
                    'facilitator' => $item->facilitator_tkf,
                    'submitted_by' => $item->user->name ?? 'N/A',
                    'created_at' => $item->created_at->format('M d, Y'),
                ];
            }),
        ];
    }

    /**
     * Get Bridging The Gap data for UC
     */
    private function getBridgingTheGapData(array $variants, ?string $startDate, ?string $endDate): array
    {
        $query = BridgingTheGap::with(['participants', 'teamMembers', 'actionPlans', 'user'])
            ->where(function ($q) use ($variants) {
                $q->whereIn('uc', $variants);
                foreach ($variants as $variant) {
                    $q->orWhere('uc', 'LIKE', $variant);
                }
            });

        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }

        $records = $query->latest()->get();

        // Calculate stats
        $totalMales = $records->sum('participants_males');
        $totalFemales = $records->sum('participants_females');
        $totalParticipants = $totalMales + $totalFemales;

        // Get action plans count
        $recordIds = $records->pluck('id');
        $totalActionPlans = BridgingTheGapActionPlan::whereIn('bridging_the_gap_id', $recordIds)->count();

        // Recent submissions (last 7 days)
        $recentCount = BridgingTheGap::where(function ($q) use ($variants) {
                $q->whereIn('uc', $variants);
                foreach ($variants as $variant) {
                    $q->orWhere('uc', 'LIKE', $variant);
                }
            })
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        return [
            'stats' => [
                'total' => $records->count(),
                'total_males' => $totalMales,
                'total_females' => $totalFemales,
                'total_participants' => $totalParticipants,
                'total_action_plans' => $totalActionPlans,
                'total_iit_members' => $records->sum(fn($r) => $r->teamMembers->count()),
                'recent' => $recentCount,
            ],
            'records' => $records->map(function ($item) {
                return [
                    'id' => $item->id,
                    'unique_id' => $item->unique_id,
                    'date' => $item->date ? $item->date->format('M d, Y') : 'N/A',
                    'venue' => $item->venue,
                    'district' => $item->district,
                    'uc' => $item->uc,
                    'participants_males' => $item->participants_males,
                    'participants_females' => $item->participants_females,
                    'total_participants' => $item->participants_males + $item->participants_females,
                    'attendance_count' => $item->participants->count(),
                    'iit_members_count' => $item->teamMembers->count(),
                    'action_plans_count' => $item->actionPlans->count(),
                    'submitted_by' => $item->user->name ?? 'N/A',
                    'created_at' => $item->created_at->format('M d, Y'),
                ];
            }),
        ];
    }

    /**
     * Get Child Line List data for UC
     */
    private function getChildLineListData(array $variants, ?string $startDate, ?string $endDate): array
    {
        $query = ChildLineList::with(['user'])
            ->where(function ($q) use ($variants) {
                $q->whereIn('uc', $variants);
                foreach ($variants as $variant) {
                    $q->orWhere('uc', 'LIKE', $variant);
                }
            });

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $records = $query->latest()->get();

        // Calculate gender stats
        $maleCount = $records->where('gender', 'Male')->count();
        $femaleCount = $records->where('gender', 'Female')->count();

        // Recent submissions (last 7 days)
        $recentCount = ChildLineList::where(function ($q) use ($variants) {
                $q->whereIn('uc', $variants);
                foreach ($variants as $variant) {
                    $q->orWhere('uc', 'LIKE', $variant);
                }
            })
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        return [
            'stats' => [
                'total' => $records->count(),
                'total_males' => $maleCount,
                'total_females' => $femaleCount,
                'recent' => $recentCount,
            ],
            'records' => $records->map(function ($item) {
                return [
                    'id' => $item->id,
                    'unique_id' => $item->unique_id,
                    'child_name' => $item->child_name,
                    'father_name' => $item->father_name,
                    'gender' => $item->gender,
                    'age_in_months' => $item->age_in_months,
                    'type' => $item->type,
                    'district' => $item->district,
                    'uc' => $item->uc,
                    'submitted_by' => $item->user->name ?? 'N/A',
                    'created_at' => $item->created_at->format('M d, Y'),
                ];
            }),
        ];
    }
}
