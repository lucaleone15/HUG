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
    Schema::create('campaign_stats', function (Blueprint $table) {
        $table->id();
        $table->unsignedInteger('donations_count')->default(0);
        $table->unsignedInteger('lives_saved')->default(0);
        $table->unsignedInteger('hug_hospitals_count')->default(0);
        $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('updated_at')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_stats');
    }
};
