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
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->enum('type', [
                'page_viewed',
                'quiz_started',
                'question_answered',
                'quiz_abandoned',
                'quiz_completed',
                'rdv_clicked',
                'kit_downloaded',
            ]);
            $table->foreignId('entreprise_id')->nullable()->constrained('entreprises')->nullOnDelete();
            $table->string('session_token')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['type', 'entreprise_id']);
            $table->index('session_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
