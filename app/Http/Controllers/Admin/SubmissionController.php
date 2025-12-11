<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use Illuminate\Http\Request;
use App\Services\LogActivity;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = FormSubmission::with('form', 'user');

        if ($request->has('form_id')) {
            $query->where('form_id', $request->form_id);
        }

        $submissions = $query->latest()->paginate(20);

        return view('admin.submissions.index', compact('submissions'));
    }

    public function show(FormSubmission $submission)
    {
        $submission->load(['form.fields', 'user', 'media', 'participants']);
        return view('admin.submissions.show', compact('submission'));
    }

    public function destroy(FormSubmission $submission)
    {
        $id = $submission->id;
        $formId = $submission->form_id;
        $submission->delete();

        LogActivity::record('submission.deleted', "Deleted submission #{$id}", ['submission_id' => $id, 'form_id' => $formId]);

        return redirect()->route('admin.submissions.index')->with('success', 'Submission deleted successfully');
    }
}
