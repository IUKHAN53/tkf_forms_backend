<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 50);
        $query = ActivityLog::with('user')->latest();
        $logs = $query->paginate($perPage == 'all' ? 999999 : (int) $perPage)->withQueryString();
        return view('admin.logs.index', compact('logs'));
    }
}
