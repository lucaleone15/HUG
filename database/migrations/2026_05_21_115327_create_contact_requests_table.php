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
    Schema::create('contact_requests', function (Blueprint $table) {
        $table->id();
        $table->enum('type', [
            'contact',
            'collecte_inscription',
            'trophee_candidature',
        ]);
        $table->string('name');
        $table->string('email');
        $table->string('company_name')->nullable();
        $table->string('subject')->nullable();
        $table->text('message');
        $table->enum('status', ['pending', 'processed', 'rejected'])->default('pending');
        $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('processed_at')->nullable();
        $table->timestamps();

        $table->index('status');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_requests');
    }
};
