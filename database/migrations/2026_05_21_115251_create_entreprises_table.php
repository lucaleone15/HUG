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
    Schema::create('entreprises', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique();
        $table->string('logo_url')->nullable();
        $table->string('primary_color')->default('#E30613');
        $table->string('secondary_color')->nullable();
        $table->string('contact_name')->nullable();
        $table->string('contact_email')->nullable();
        $table->unsignedInteger('employee_count')->nullable();
        $table->boolean('is_active')->default(true);
        $table->boolean('is_labelled')->default(false);
        $table->unsignedTinyInteger('trophy_rank')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entreprises');
    }
};
