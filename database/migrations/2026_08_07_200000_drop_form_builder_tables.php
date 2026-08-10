<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Drops the generic form-builder schema.
 *
 * The builder (Form / FormField / FormSubmission / FormSubmissionParticipant)
 * was never adopted — the five core forms are all first-class models with their
 * own tables. At the time of removal production held a single demo form
 * ("Site Inspection", 6 fields) and zero submissions.
 *
 * `media` belonged to spatie/laravel-medialibrary, which only ever backed the
 * builder's file/image/signature fields and has been removed as a dependency.
 *
 * Irreversible by design: down() would recreate empty tables for code that no
 * longer exists. Roll back by reverting the commit, not the migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Children first — these tables carry foreign keys to their parents.
        Schema::dropIfExists('media');
        Schema::dropIfExists('form_submission_participants');
        Schema::dropIfExists('form_submissions');
        Schema::dropIfExists('form_fields');
        Schema::dropIfExists('forms');
    }

    public function down(): void
    {
        // Intentionally irreversible.
    }
};
