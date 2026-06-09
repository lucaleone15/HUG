<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DemoResetCommand extends Command
{
    protected $signature   = 'demo:reset {--seed : Recharger les données de démo après le reset}';
    protected $description = 'Vide toutes les données métier en préservant les admins existants.';

    public function handle(): int
    {
        if (! $this->confirm('Vider entreprises, submissions, analytics et collectes ? Les admins sont préservés.', true)) {
            $this->info('Annulé.');
            return self::SUCCESS;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('analytics_events')->truncate();
        DB::table('submissions')->truncate();
        DB::table('collectes')->truncate();
        DB::table('campaign_stats')->truncate();
        DB::table('entreprises')->truncate();
        Schema::enableForeignKeyConstraints();

        $this->info('Tables vidées. Admins préservés.');

        if ($this->option('seed')) {
            $this->info('Chargement des données de démo...');
            $this->call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
        }

        return self::SUCCESS;
    }
}
