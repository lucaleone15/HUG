<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entreprises', function (Blueprint $table) {
            $table->string('access_token', 48)->nullable()->unique()->after('slug');
        });

        // Générer un token pour chaque entreprise existante
        foreach (DB::table('entreprises')->orderBy('id')->cursor() as $row) {
            DB::table('entreprises')->where('id', $row->id)->update([
                'access_token' => Str::random(48),
            ]);
        }

        Schema::table('entreprises', function (Blueprint $table) {
            $table->string('access_token', 48)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('entreprises', function (Blueprint $table) {
            $table->dropColumn('access_token');
        });
    }
};
