<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            // Polymorphic relationship - can belong to religious_leaders, community_barriers, or healthcare_barriers
            $table->morphs('participantable');
            $table->integer('sr_no')->nullable();
            $table->string('name');
            $table->string('title_designation')->nullable();
            $table->string('occupation')->nullable();
            $table->text('address')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('cnic')->nullable();
            $table->string('gender')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
