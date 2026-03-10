<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaccination_records', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Site info
            $table->string('fix_site')->nullable();
            $table->string('uc')->nullable();
            $table->string('district')->nullable();

            // Child info
            $table->unsignedInteger('serial_number')->default(0);
            $table->string('child_name');
            $table->string('father_name');
            $table->string('age')->nullable();
            $table->string('address')->nullable();
            $table->string('contact_number')->nullable();

            // Category & vaccination
            $table->enum('category', ['Defaulter', 'Refusal', 'Zero Dose'])->default('Defaulter');
            $table->enum('vaccinated', ['YES', 'NO'])->default('NO');
            $table->date('date_of_vaccination')->nullable();

            // Community member
            $table->string('community_member_name')->nullable();
            $table->string('community_member_contact')->nullable();

            // GPS & device
            $table->string('gps_coordinates')->nullable();
            $table->float('latitude', 10, 6)->nullable();
            $table->float('longitude', 10, 6)->nullable();
            $table->string('ip_address')->nullable();
            $table->json('device_info')->nullable();

            // Timestamps
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            // Indexes for common queries
            $table->index('category');
            $table->index('vaccinated');
            $table->index(['district', 'uc', 'fix_site']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaccination_records');
    }
};
