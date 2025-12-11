<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('district');
            $table->string('town');
            $table->string('uc_name');
            $table->string('fix_site');
            $table->string('outreach_name');
            $table->string('outreach_coordinates')->nullable();
            $table->string('area_name');
            $table->string('assigned_aic');
            $table->string('aic_contact')->nullable();
            $table->string('assigned_cm');
            $table->string('cm_contact')->nullable();
            $table->integer('total_population');
            $table->integer('total_under_2_years');
            $table->integer('total_zero_dose');
            $table->integer('total_defaulter');
            $table->integer('total_refusal');
            $table->integer('total_boys_under_2')->nullable();
            $table->integer('total_girls_under_2')->nullable();
            $table->string('major_ethnicity')->nullable();
            $table->string('major_languages')->nullable();
            $table->text('existing_committees')->nullable();
            $table->string('nearest_phf')->nullable();
            $table->string('hf_incharge_name')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_mappings');
    }
};
