<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bridging_the_gaps', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Attendance Tab Fields
            $table->dateTime('date');
            $table->string('venue');
            $table->string('district');
            $table->string('uc');
            $table->string('fix_site');
            $table->integer('participants_males')->default(0);
            $table->integer('participants_females')->default(0);

            // Metadata
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('ip_address')->nullable();
            $table->json('device_info')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bridging_the_gaps');
    }
};
