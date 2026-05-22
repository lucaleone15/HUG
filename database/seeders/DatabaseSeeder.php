<?php

namespace Database\Seeders;

use App\Models\CampaignStats;
use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin HUG',
            'email' => 'admin@hug-ge.ch',
            'is_admin' => true,
        ]);

        CampaignStats::create([
            'donations_count'     => 0,
            'lives_saved'         => 0,
            'hug_hospitals_count' => 0,
        ]);

        Entreprise::insert([
            [
                'name'          => 'Banque Cantonale de Genève',
                'slug'          => 'bcge',
                'primary_color' => '#003F7D',
                'is_active'     => true,
                'is_labelled'   => false,
                'is_validated'  => false,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'Groupe Mutuel',
                'slug'          => 'groupe-mutuel',
                'primary_color' => '#E30613',
                'is_active'     => true,
                'is_labelled'   => true,
                'is_validated'  => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'Firmenich',
                'slug'          => 'firmenich',
                'primary_color' => '#00833E',
                'is_active'     => true,
                'is_labelled'   => false,
                'is_validated'  => false,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }
}
