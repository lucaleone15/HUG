<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entreprises', function (Blueprint $table) {
            $table->dropColumn(['rdv_url', 'rdv_date']);
        });
    }

    public function down(): void
    {
        Schema::table('entreprises', function (Blueprint $table) {
            $table->string('rdv_url')->nullable()->after('wants_trophy');
            $table->date('rdv_date')->nullable()->after('rdv_url');
        });
    }
};
