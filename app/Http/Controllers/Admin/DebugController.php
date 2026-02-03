<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DebugController extends Controller
{
    public function index(): View
    {
        return view('admin.debug.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:5000'],
            'contact_email' => ['nullable', 'email', 'max:255'],
        ]);

        $payload = [
            'timestamp' => now()->toDateTimeString(),
            'user_id' => optional($request->user())->id,
            'user_name' => optional($request->user())->name,
            'contact_email' => $validated['contact_email'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'app_url' => config('app.url'),
            'app_env' => config('app.env'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ];

        Storage::disk('local')->append('debug-reports.log', json_encode($payload));

        return back()->with('success', 'Debug report submitted.');
    }
}
