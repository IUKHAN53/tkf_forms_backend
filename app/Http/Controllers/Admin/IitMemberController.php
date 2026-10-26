<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BridgingTheGap;
use App\Models\BridgingTheGapTeamMember;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

/**
 * IIT (Immunization Improvement Team) members.
 *
 * An IIT member is a BridgingTheGapTeamMember row: a reference from one Bridging
 * The Gap session to a Participant. Members belong to a single session and may
 * only be chosen from that session's own attendance sheet — "registering" a new
 * person adds them to the attendance sheet first, then to the team. (A couple of
 * legacy members were picked from FGD participants by the mobile app; they still
 * list, edit and remove normally.)
 *
 * Every write action redirects back, so the same endpoints serve both this page
 * and the IIT section on the Bridging The Gap detail page.
 */
class IitMemberController extends Controller
{
    public function index(Request $request)
    {
        $members = $this->filteredQuery($request)
            ->with(['participant', 'bridgingTheGap'])
            ->orderByDesc(
                BridgingTheGap::select('date')->whereColumn('bridging_the_gaps.id', 'bridging_the_gap_team_members.bridging_the_gap_id')
            )
            ->orderBy('id')
            ->paginate($this->perPage($request))
            ->withQueryString();

        // Stats follow the page filters, like the FGDs screens.
        $filtered = $this->filteredQuery($request)->with('participant:id,gender', 'bridgingTheGap:id,uc')->get();

        $stats = [
            'total' => $filtered->count(),
            'sessions' => $filtered->pluck('bridging_the_gap_id')->unique()->count(),
            'males' => $filtered->filter(fn ($m) => $m->participant?->gender === 'Male')->count(),
            'females' => $filtered->filter(fn ($m) => $m->participant?->gender === 'Female')->count(),
            'ucs' => $filtered->map(fn ($m) => DashboardController::getConsolidatedUcName($m->bridgingTheGap?->uc) ?: $m->bridgingTheGap?->uc)
                ->filter()->unique()->count(),
        ];
        $stats['avg_per_session'] = $stats['sessions'] > 0 ? round($stats['total'] / $stats['sessions'], 1) : 0;

        $sessions = BridgingTheGap::orderBy('uc')->orderByDesc('date')
            ->get(['id', 'unique_id', 'uc', 'fix_site', 'venue', 'date', 'district']);

        return view('admin.iit-members.index', [
            'members' => $members,
            'stats' => $stats,
            'sessions' => $sessions,
            'districts' => $sessions->pluck('district')->filter()->unique()->sort()->values(),
            'ucs' => $this->ucOptions($sessions),
            'fixSites' => $sessions->pluck('fix_site')->filter()->unique()->sort()->values(),
        ]);
    }

    /**
     * People on a session's attendance sheet who are not yet on its IIT team —
     * the only people who may be picked for that session.
     */
    public function candidates(Request $request): JsonResponse
    {
        $session = BridgingTheGap::findOrFail($request->integer('bridging_the_gap_id'));
        $memberIds = $session->teamMembers()->pluck('participant_id');

        $people = $session->participants()
            ->whereNotIn('id', $memberIds)
            ->orderBy('sr_no')->orderBy('id')
            ->get()
            ->map(fn (Participant $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'role' => $p->role,
                'contact_no' => $p->contact_no,
                'gender' => $p->gender,
            ]);

        return response()->json([
            'session' => ['id' => $session->id, 'unique_id' => $session->unique_id],
            'candidates' => $people,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'bridging_the_gap_id' => 'required|integer|exists:bridging_the_gaps,id',
            'participant_id' => 'nullable|integer',
        ]);

        $session = BridgingTheGap::findOrFail($request->integer('bridging_the_gap_id'));

        if ($request->filled('participant_id')) {
            $participant = Participant::find($request->integer('participant_id'));

            if (! $participant || ! $session->ownsParticipant($participant)) {
                return back()->withInput()->with('error', "Only people on session {$session->unique_id}'s attendance sheet can be chosen as its IIT members.");
            }
        } else {
            // Registering someone new: they join the attendance sheet first.
            $participant = $session->addAttendee($request->validate(Participant::adminRules()));
        }

        $exists = BridgingTheGapTeamMember::where('bridging_the_gap_id', $session->id)
            ->where('participant_id', $participant->id)
            ->exists();

        if ($exists) {
            return back()->with('error', "{$participant->name} is already an IIT member of {$session->unique_id}.");
        }

        BridgingTheGapTeamMember::create([
            'bridging_the_gap_id' => $session->id,
            'participant_id' => $participant->id,
            'source_type' => 'bridging_the_gap',
            'source_id' => $session->id,
        ]);

        return back()->with('success', "{$participant->name} added to the IIT team of {$session->unique_id}.");
    }

    /** Edit the member's personal details (name, role, contact…). */
    public function update(Request $request, BridgingTheGapTeamMember $member)
    {
        if (! $member->participant) {
            return back()->with('error', 'This member\'s participant record no longer exists.');
        }

        $member->participant->update($request->validate(Participant::adminRules()));

        return back()->with('success', "{$member->participant->name}'s details were updated.");
    }

    /** Remove from the IIT team. The person stays on the attendance sheet. */
    public function destroy(BridgingTheGapTeamMember $member)
    {
        $name = $member->participant->name ?? 'Member';
        $member->delete();

        return back()->with('success', "{$name} removed from the IIT team.");
    }

    public function export(Request $request)
    {
        $members = $this->filteredQuery($request)->with(['participant', 'bridgingTheGap'])->orderBy('bridging_the_gap_id')->get();

        $columns = ['Name', 'Role / Designation', 'Contact', 'CNIC', 'Gender', 'Occupation',
            'Session Form ID', 'Session Date', 'District', 'UC', 'Fix Site', 'Venue', 'Source', 'Added On'];

        $callback = function () use ($members, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($members as $m) {
                $p = $m->participant;
                $s = $m->bridgingTheGap;
                fputcsv($file, [
                    $p->name ?? '',
                    $p?->role ?? '',
                    $p->contact_no ?? '',
                    $p->cnic ?? '',
                    $p->gender ?? '',
                    $p->occupation ?? '',
                    $s->unique_id ?? '',
                    $s?->date?->format('Y-m-d') ?? '',
                    $s->district ?? '',
                    $s->uc ?? '',
                    $s->fix_site ?? '',
                    $s->venue ?? '',
                    $this->sourceLabel($m->source_type),
                    $m->created_at?->format('Y-m-d') ?? '',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="iit_members_'.date('Y-m-d_His').'.csv"',
        ]);
    }

    /* ===================================================================== */

    /** Members matching the page filters (search, district, UC, fix site, session, gender). */
    private function filteredQuery(Request $request): Builder
    {
        $query = BridgingTheGapTeamMember::query()->whereHas('participant');

        if ($request->filled('search')) {
            $search = $request->string('search')->trim()->toString();
            $query->whereHas('participant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('contact_no', 'like', "%{$search}%")
                    ->orWhere('cnic', 'like', "%{$search}%")
                    ->orWhere('title_designation', 'like', "%{$search}%")
                    ->orWhere('occupation', 'like', "%{$search}%");
            });
        }

        if ($request->filled('gender')) {
            $query->whereHas('participant', fn ($q) => $q->where('gender', $request->gender));
        }

        if ($request->filled('bridging_the_gap_id')) {
            $query->where('bridging_the_gap_id', $request->integer('bridging_the_gap_id'));
        }

        if ($request->hasAny(['district', 'uc', 'fix_site'])) {
            $query->whereHas('bridgingTheGap', function ($q) use ($request) {
                if ($request->filled('district')) {
                    $q->where('district', $request->district);
                }
                if ($request->filled('uc')) {
                    $uc = (string) $request->uc;
                    $q->whereIn('uc', array_values(array_unique(array_merge([$uc], DashboardController::getUcVariants($uc)))));
                }
                if ($request->filled('fix_site')) {
                    $q->where('fix_site', $request->fix_site);
                }
            });
        }

        return $query;
    }

    /** Consolidated UC names for the filter dropdown. */
    private function ucOptions($sessions)
    {
        return $sessions->pluck('uc')->filter()
            ->map(fn ($uc) => DashboardController::getConsolidatedUcName($uc) ?: $uc)
            ->unique()->sort(SORT_NATURAL | SORT_FLAG_CASE)->values();
    }

    private function perPage(Request $request): int
    {
        $perPage = $request->input('per_page', 25);

        return $perPage === 'all' ? 100000 : max(1, min((int) $perPage, 500));
    }

    public static function sourceLabel(?string $source): string
    {
        return match ($source) {
            'bridging_the_gap' => 'Attendance',
            'fgds_community', 'community_barrier' => 'FGDs-Community',
            'fgds_health_workers', 'healthcare_barrier' => 'FGDs-Health Workers',
            default => $source ?: '—',
        };
    }
}
