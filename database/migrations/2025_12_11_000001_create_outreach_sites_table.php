<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('outreach_sites', function (Blueprint $table) {
            $table->id();
            $table->string('district');
            $table->string('union_council');
            $table->string('fix_site');
            $table->string('outreach_site')->nullable();
            $table->string('coordinates')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outreach_sites');
    }
};
