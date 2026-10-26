<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Participant extends Model
{
    protected $fillable = [
        'participantable_id',
        'participantable_type',
        'sr_no',
        'name',
        'title_designation',
        'designation',
        'occupation',
        'address',
        'contact_no',
        'cnic',
        'gender',
    ];

    protected $casts = [
        'sr_no' => 'integer',
    ];

    public function participantable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Validation rules for creating/editing a person from the admin panel
     * (BTG attendance sheet and IIT members). Only the name is required.
     */
    public static function adminRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:Male,Female',
            'title_designation' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'contact_no' => 'nullable|string|max:30',
            'cnic' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ];
    }

    /** The person's role for display: designation, falling back to occupation. */
    public function getRoleAttribute(): ?string
    {
        return $this->title_designation ?: ($this->designation ?: $this->occupation);
    }

    /**
     * Delete a participant safely: drop IIT team-member references first and
     * detach any CLM community member pointing at it (that column has no FK).
     */
    public function deleteWithReferences(): void
    {
        BridgingTheGapTeamMember::where('participant_id', $this->id)->delete();
        CommunityMember::where('participant_id', $this->id)->update(['participant_id' => null]);
        $this->delete();
    }
}
