<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\LogActivity;

class CommunityMemberController extends Controller
{
    public function index(Request $request)
    {
        $query = CommunityMember::orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('district', 'like', "%{$search}%")
                    ->orWhere('uc', 'like', "%{$search}%");
            });
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        $members = $query->paginate(15)->withQueryString();
        $districts = CommunityMember::distinct()->whereNotNull('district')->pluck('district')->sort()->values();

        return view('admin.community-members.index', compact('members', 'districts'));
    }

    public function create()
    {
        return view('admin.community-members.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50|unique:community_members,phone',
            'password' => 'required|string|min:6|confirmed',
            'district' => 'nullable|string|max:255',
            'uc' => 'nullable|string|max:255',
            'fix_site' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $member = CommunityMember::create($validated);

        LogActivity::record('community_member.created', "Created community member {$member->name} ({$member->phone})", ['member_id' => $member->id]);

        return redirect()->route('admin.community-members.index')->with('success', 'Community member created');
    }

    public function edit(CommunityMember $communityMember)
    {
        return view('admin.community-members.edit', ['member' => $communityMember]);
    }

    public function update(Request $request, CommunityMember $communityMember)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50|unique:community_members,phone,' . $communityMember->id,
            'password' => 'nullable|string|min:6|confirmed',
            'district' => 'nullable|string|max:255',
            'uc' => 'nullable|string|max:255',
            'fix_site' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $communityMember->name = $validated['name'];
        $communityMember->phone = $validated['phone'];
        $communityMember->district = $validated['district'] ?? null;
        $communityMember->uc = $validated['uc'] ?? null;
        $communityMember->fix_site = $validated['fix_site'] ?? null;
        $communityMember->is_active = $request->boolean('is_active', false);

        if (!empty($validated['password'])) {
            $communityMember->password = Hash::make($validated['password']);
        }

        $communityMember->save();

        LogActivity::record('community_member.updated', "Updated community member {$communityMember->name} ({$communityMember->phone})", ['member_id' => $communityMember->id]);

        return redirect()->route('admin.community-members.index')->with('success', 'Community member updated');
    }

    public function destroy(CommunityMember $communityMember)
    {
        $name = $communityMember->name;
        $id = $communityMember->id;
        $communityMember->delete();

        LogActivity::record('community_member.deleted', "Deleted community member {$name}", ['member_id' => $id]);

        return redirect()->route('admin.community-members.index')->with('success', 'Community member deleted');
    }
}
