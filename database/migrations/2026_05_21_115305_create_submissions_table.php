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
    Schema::create('submissions', function (Blueprint $table) {
        $table->id();
        $table->string('session_token')->unique();
        $table->foreignId('entreprise_id')->constrained('entreprises')->cascadeOnDelete();
        $table->boolean('is_eligible')->nullable();
        $table->timestamp('completed_at')->nullable();
        $table->timestamps();

        $table->index(['entreprise_id', 'is_eligible']);

    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
