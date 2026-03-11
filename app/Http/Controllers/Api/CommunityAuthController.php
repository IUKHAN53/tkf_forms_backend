<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CommunityAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $member = CommunityMember::where('phone', $validated['phone'])->first();

        if (!$member || !Hash::check($validated['password'], $member->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (!$member->is_active) {
            return response()->json(['message' => 'Account is deactivated. Contact your administrator.'], 403);
        }

        $token = $member->createToken('clm-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $member,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
