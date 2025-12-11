<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmissionParticipant extends Model
{
    protected $fillable = [
        'form_submission_id',
        'sr_no',
        'name',
        'title_designation',
        'occupation',
        'address',
        'contact_no',
        'cnic',
        'gender',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(FormSubmission::class, 'form_submission_id');
    }
}
