<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('draft_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('division');
            $table->string('district');
            $table->string('town');
            $table->string('uc');
            $table->string('outreach');
            $table->string('child_name');
            $table->string('father_name');
            $table->string('gender');
            $table->date('date_of_birth');
            $table->integer('age_in_months');
            $table->string('father_cnic')->nullable();
            $table->string('house_number')->nullable();
            $table->text('address');
            $table->string('guardian_phone')->nullable();
            $table->string('type'); // Zero Dose or Defaulter
            $table->json('missed_vaccines'); // Array of missed vaccines
            $table->string('reasons_of_missing');
            $table->text('plan_for_coverage');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('draft_lists');
    }
};
