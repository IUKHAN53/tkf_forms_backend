<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_forms' => Form::count(),
            'active_forms' => Form::where('is_active', true)->count(),
            'total_submissions' => FormSubmission::count(),
            'recent_submissions' => FormSubmission::with('form', 'user')->latest()->take(10)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
