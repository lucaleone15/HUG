<?php

namespace Database\Seeders;

use App\Models\CampaignStats;
use Illuminate\Database\Seeder;

/**
 * Seeder minimal pour la production.
 * Idempotent — peut être exécuté plusieurs fois sans effet de bord.
 *
 * Usage :
 *   php artisan db:seed --class=ProductionSeeder --force
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        // Ligne unique obligatoire pour /api/stats et le dashboard
        CampaignStats::getInstance();

        $this->command->info('ProductionSeeder : CampaignStats initialisée.');
    }
}
