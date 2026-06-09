<?php

namespace Database\Seeders;

use App\Models\CampaignStats;
use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Seeder de démonstration — couvre tous les états possibles d'une entreprise.
 * Vide toutes les données métier et les recrée ; les admins existants sont préservés.
 *
 * Usage :  php artisan db:seed --class=DemoSeeder [--force]
 * Reset :  relancer la même commande — idempotent
 */
class DemoSeeder extends Seeder
{
    private int $quizQuestionCount;

    public function run(): void
    {
        $quiz = json_decode(file_get_contents(resource_path('quiz/quiz.json')), true);
        $this->quizQuestionCount = count($quiz['questions']);

        $this->reset();
        $this->ensureAdmin();
        $this->seedCampaignStats();
        $entreprises = $this->seedEntreprises();
        $this->seedSubmissionsAndAnalytics($entreprises);

        $this->command->info('DemoSeeder terminé — ' . count($entreprises) . ' entreprises insérées. Admins préservés.');
    }

    // -------------------------------------------------------------------------
    // Reset
    // -------------------------------------------------------------------------

    private function reset(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('analytics_events')->truncate();
        DB::table('submissions')->truncate();
        DB::table('collectes')->truncate();
        DB::table('campaign_stats')->truncate();
        DB::table('entreprises')->truncate();
        Schema::enableForeignKeyConstraints();
    }

    private function ensureAdmin(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@hug.ch'],
            [
                'name'     => 'Admin HUG',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'julie.dumont@hug.ch'],
            [
                'name'     => 'Julie Dumont',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );
    }

    // -------------------------------------------------------------------------
    // Campaign stats
    // -------------------------------------------------------------------------

    private function seedCampaignStats(): void
    {
        $admin = User::where('is_admin', true)->first();

        CampaignStats::create([
            'donations_count'     => 847,
            'lives_saved'         => 2541,
            'hug_hospitals_count' => 3,
            'updated_by'          => $admin?->id,
        ]);
    }

    // -------------------------------------------------------------------------
    // Entreprises + collectes
    // -------------------------------------------------------------------------

    private function seedEntreprises(): array
    {
        $now  = now();
        $data = $this->companiesData();

        $rows = [];
        foreach ($data as $row) {
            $rows[] = array_merge($row, [
                'access_token' => Str::random(48),
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }

        DB::table('entreprises')->insert($rows);

        $slugs       = array_column($data, 'slug');
        $entreprises = Entreprise::whereIn('slug', $slugs)->get()->keyBy('slug');

        $collectes = [];
        foreach ($data as $row) {
            if (empty($row['rdv_url'])) continue;
            $entreprise = $entreprises->get($row['slug']);
            if (! $entreprise) continue;

            $collectes[] = [
                'entreprise_id' => $entreprise->id,
                'ondoc_url'     => $row['rdv_url'],
                'rdv_date'      => $row['rdv_date'] ?? null,
                'label'         => null,
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        if ($collectes) {
            DB::table('collectes')->insert($collectes);
        }

        return $entreprises->toArray();
    }

    private function companiesData(): array
    {
        return [

            // ================================================================
            // ÉTAT 1 — Classement trophy
            // Labellisées · validées · actives · rang attribué · collecte OnDoc
            // ================================================================

            [
                'name'            => 'Banque Cantonale de Genève',
                'slug'            => 'bcge',
                'type'            => 'banque',
                'primary_color'   => '#003F7D',
                'secondary_color' => '#C8A876',
                'logo_url'        => 'https://logo.clearbit.com/bcge.ch',
                'employee_count'  => 850,
                'trophy_rank'     => 1,
                'wants_trophy'    => true,
                'is_labelled'     => true,
                'is_validated'    => true,
                'is_active'       => true,
                'is_public'       => true,
                'locale'          => 'fr',
                'contact_name'    => 'Marc Fontaine',
                'contact_email'   => 'rh@bcge.ch',
                'rdv_url'         => 'https://www.onedoc.ch/fr/don-de-sang/geneve/collecte-bcge-2026',
                'rdv_date'        => '2026-09-15',
            ],
            [
                'name'            => 'Firmenich',
                'slug'            => 'firmenich',
                'type'            => 'industrie',
                'primary_color'   => '#00833E',
                'secondary_color' => '#C8A700',
                'logo_url'        => 'https://logo.clearbit.com/firmenich.com',
                'employee_count'  => 3200,
                'trophy_rank'     => 2,
                'wants_trophy'    => true,
                'is_labelled'     => true,
                'is_validated'    => true,
                'is_active'       => true,
                'is_public'       => true,
                'locale'          => 'fr',
                'contact_name'    => 'Sophie Müller',
                'contact_email'   => 'wellbeing@firmenich.com',
                'rdv_url'         => 'https://www.onedoc.ch/fr/don-de-sang/geneve/collecte-firmenich-2026',
                'rdv_date'        => '2026-09-22',
            ],
            [
                'name'            => 'Groupe Mutuel',
                'slug'            => 'groupe-mutuel',
                'type'            => 'assurance',
                'primary_color'   => '#E30613',
                'secondary_color' => '#9B0000',
                'logo_url'        => 'https://logo.clearbit.com/groupemutuel.ch',
                'employee_count'  => 1400,
                'trophy_rank'     => 3,
                'wants_trophy'    => true,
                'is_labelled'     => true,
                'is_validated'    => true,
                'is_active'       => true,
                'is_public'       => true,
                'locale'          => 'fr',
                'contact_name'    => 'Alain Pernet',
                'contact_email'   => 'rh@groupemutuel.ch',
                'rdv_url'         => 'https://www.onedoc.ch/fr/don-de-sang/geneve/collecte-groupe-mutuel-2026',
                'rdv_date'        => '2026-10-03',
            ],
            [
                'name'            => 'SGS',
                'slug'            => 'sgs',
                'type'            => 'technologie',
                'primary_color'   => '#009FE3',
                'secondary_color' => '#003865',
                'logo_url'        => 'https://logo.clearbit.com/sgs.com',
                'employee_count'  => 960,
                'trophy_rank'     => 4,
                'wants_trophy'    => true,
                'is_labelled'     => true,
                'is_validated'    => true,
                'is_active'       => true,
                'is_public'       => true,
                'locale'          => 'fr',
                'contact_name'    => 'Céline Rochat',
                'contact_email'   => 'hr@sgs.com',
                'rdv_url'         => 'https://www.onedoc.ch/fr/don-de-sang/geneve/collecte-sgs-2026',
                'rdv_date'        => '2026-10-10',
            ],
            [
                'name'            => 'SIG — Services Industriels de Genève',
                'slug'            => 'sig',
                'type'            => 'service',
                'primary_color'   => '#F7941D',
                'secondary_color' => '#004A97',
                'logo_url'        => null,
                'employee_count'  => 1900,
                'trophy_rank'     => 5,
                'wants_trophy'    => true,
                'is_labelled'     => true,
                'is_validated'    => true,
                'is_active'       => true,
                'is_public'       => true,
                'locale'          => 'fr',
                'contact_name'    => 'Pierre-Alain Duc',
                'contact_email'   => 'rh@sig-ge.ch',
                'rdv_url'         => 'https://www.onedoc.ch/fr/don-de-sang/geneve/collecte-sig-2026',
                'rdv_date'        => '2026-10-17',
            ],

            // ================================================================
            // ÉTAT 2 — Veulent le trophy, pas encore classées
            // Labellisées · validées · actives · wants_trophy = true · rang null
            // ================================================================

            [
                'name'            => 'Julius Baer',
                'slug'            => 'julius-baer',
                'type'            => 'banque',
                'primary_color'   => '#836B28',
                'secondary_color' => '#2C2C2C',
                'logo_url'        => 'https://logo.clearbit.com/juliusbaer.com',
                'employee_count'  => 7000,
                'trophy_rank'     => null,
                'wants_trophy'    => true,
                'is_labelled'     => true,
                'is_validated'    => true,
                'is_active'       => true,
                'is_public'       => true,
                'locale'          => 'de',
                'contact_name'    => 'Katharina Meier',
                'contact_email'   => 'hr@juliusbaer.com',
                'rdv_url'         => 'https://www.onedoc.ch/fr/don-de-sang/geneve/collecte-julius-baer-2026',
                'rdv_date'        => '2026-11-05',
            ],
            [
                'name'            => 'AXA Suisse',
                'slug'            => 'axa-suisse',
                'type'            => 'assurance',
                'primary_color'   => '#00008F',
                'secondary_color' => '#FF1721',
                'logo_url'        => 'https://logo.clearbit.com/axa.ch',
                'employee_count'  => 8000,
                'trophy_rank'     => null,
                'wants_trophy'    => true,
                'is_labelled'     => true,
                'is_validated'    => true,
                'is_active'       => true,
                'is_public'       => true,
                'locale'          => 'fr',
                'contact_name'    => 'Christine Vogel',
                'contact_email'   => 'rh@axa.ch',
                'rdv_url'         => 'https://www.onedoc.ch/fr/don-de-sang/geneve/collecte-axa-2026',
                'rdv_date'        => '2026-11-12',
            ],
            [
                'name'            => 'Logitech',
                'slug'            => 'logitech',
                'type'            => 'technologie',
                'primary_color'   => '#004A97',
                'secondary_color' => '#00B0F0',
                'logo_url'        => 'https://logo.clearbit.com/logitech.com',
                'employee_count'  => 9000,
                'trophy_rank'     => null,
                'wants_trophy'    => true,
                'is_labelled'     => true,
                'is_validated'    => true,
                'is_active'       => true,
                'is_public'       => true,
                'locale'          => 'fr',
                'contact_name'    => 'David Schmid',
                'contact_email'   => 'hr@logitech.com',
                'rdv_url'         => null,
                'rdv_date'        => null,
            ],

            // ================================================================
            // ÉTAT 3 — Labellisées, validées, actives — pas de classement
            // ================================================================

            [
                'name'            => 'CERN',
                'slug'            => 'cern',
                'type'            => 'technologie',
                'primary_color'   => '#0053A0',
                'secondary_color' => '#9B1A1A',
                'logo_url'        => 'https://logo.clearbit.com/cern.ch',
                'employee_count'  => 2500,
                'trophy_rank'     => null,
                'wants_trophy'    => false,
                'is_labelled'     => true,
                'is_validated'    => true,
                'is_active'       => true,
                'is_public'       => true,
                'locale'          => 'fr',
                'contact_name'    => 'Elena Kovač',
                'contact_email'   => 'hr@cern.ch',
                'rdv_url'         => null,
                'rdv_date'        => null,
            ],
            [
                'name'            => 'Pictet & Cie',
                'slug'            => 'pictet',
                'type'            => 'banque',
                'primary_color'   => '#1B4B8B',
                'secondary_color' => '#B8975A',
                'logo_url'        => 'https://logo.clearbit.com/pictet.com',
                'employee_count'  => 5000,
                'trophy_rank'     => null,
                'wants_trophy'    => false,
                'is_labelled'     => true,
                'is_validated'    => true,
                'is_active'       => true,
                'is_public'       => true,
                'locale'          => 'fr',
                'contact_name'    => 'Nathalie Clerc',
                'contact_email'   => 'rh@pictet.com',
                'rdv_url'         => 'https://www.onedoc.ch/fr/don-de-sang/geneve/collecte-pictet-2026',
                'rdv_date'        => '2026-11-20',
            ],
            [
                'name'            => 'Richemont',
                'slug'            => 'richemont',
                'type'            => 'industrie',
                'primary_color'   => '#1A1A2E',
                'secondary_color' => '#C5A028',
                'logo_url'        => 'https://logo.clearbit.com/richemont.com',
                'employee_count'  => 35000,
                'trophy_rank'     => null,
                'wants_trophy'    => false,
                'is_labelled'     => true,
                'is_validated'    => true,
                'is_active'       => true,
                'is_public'       => true,
                'locale'          => 'fr',
                'contact_name'    => 'Thomas Bauer',
                'contact_email'   => 'hr@richemont.com',
                'rdv_url'         => null,
                'rdv_date'        => null,
            ],
            [
                'name'            => 'Roche',
                'slug'            => 'roche',
                'type'            => 'sante',
                'primary_color'   => '#009FE3',
                'secondary_color' => '#00456A',
                'logo_url'        => 'https://logo.clearbit.com/roche.com',
                'employee_count'  => 100000,
                'trophy_rank'     => null,
                'wants_trophy'    => false,
                'is_labelled'     => true,
                'is_validated'    => true,
                'is_active'       => true,
                'is_public'       => true,
                'locale'          => 'fr',
                'contact_name'    => 'Isabelle Favre',
                'contact_email'   => 'hr@roche.com',
                'rdv_url'         => 'https://www.onedoc.ch/fr/don-de-sang/geneve/collecte-roche-2026',
                'rdv_date'        => '2026-12-01',
            ],
            [
                'name'            => 'La Poste Suisse',
                'slug'            => 'la-poste',
                'type'            => 'service',
                'primary_color'   => '#FFCC00',
                'secondary_color' => '#CC0000',
                'logo_url'        => 'https://logo.clearbit.com/post.ch',
                'employee_count'  => 50000,
                'trophy_rank'     => null,
                'wants_trophy'    => false,
                'is_labelled'     => true,
                'is_validated'    => true,
                'is_active'       => true,
                'is_public'       => true,
                'locale'          => 'fr',
                'contact_name'    => 'Martine Dupont',
                'contact_email'   => 'rh@post.ch',
                'rdv_url'         => 'https://www.onedoc.ch/fr/don-de-sang/geneve/collecte-la-poste-2026',
                'rdv_date'        => '2026-12-08',
            ],
            [
                // Cas spécial : labellisée mais is_public = false → invisible dans le classement public
                'name'            => 'Rolex SA',
                'slug'            => 'rolex',
                'type'            => 'industrie',
                'primary_color'   => '#1B4332',
                'secondary_color' => '#A07850',
                'logo_url'        => 'https://logo.clearbit.com/rolex.com',
                'employee_count'  => 9000,
                'trophy_rank'     => null,
                'wants_trophy'    => false,
                'is_labelled'     => true,
                'is_validated'    => true,
                'is_active'       => true,
                'is_public'       => false,
                'locale'          => 'fr',
                'contact_name'    => 'Jean-Pierre Magnin',
                'contact_email'   => 'hr@rolex.com',
                'rdv_url'         => null,
                'rdv_date'        => null,
            ],
            [
                'name'            => 'Migros Genève',
                'slug'            => 'migros-geneve',
                'type'            => 'commerce',
                'primary_color'   => '#FF6600',
                'secondary_color' => '#231F20',
                'logo_url'        => 'https://logo.clearbit.com/migros.ch',
                'employee_count'  => 5000,
                'trophy_rank'     => null,
                'wants_trophy'    => false,
                'is_labelled'     => true,
                'is_validated'    => true,
                'is_active'       => true,
                'is_public'       => true,
                'locale'          => 'fr',
                'contact_name'    => 'Anne-Marie Favre',
                'contact_email'   => 'rh@migros.ch',
                'rdv_url'         => null,
                'rdv_date'        => null,
            ],

            // ================================================================
            // ÉTAT 4 — Validées, actives, pas encore labellisées
            // ================================================================

            [
                'name'            => 'HEIG',
                'slug'            => 'heig',
                'type'            => 'education',
                'primary_color'   => '#E30613',
                'secondary_color' => '#1A1A1A',
                'logo_url'        => null,
                'employee_count'  => 100,
                'trophy_rank'     => null,
                'wants_trophy'    => false,
                'is_labelled'     => false,
                'is_validated'    => true,
                'is_active'       => true,
                'is_public'       => true,
                'locale'          => 'fr',
                'contact_name'    => 'Laurent Gasser',
                'contact_email'   => 'rh@heig-vd.ch',
                'rdv_url'         => null,
                'rdv_date'        => null,
            ],
            [
                'name'            => 'HES-SO Genève',
                'slug'            => 'hes-so-ge',
                'type'            => 'education',
                'primary_color'   => '#E30613',
                'secondary_color' => '#003366',
                'logo_url'        => null,
                'employee_count'  => 700,
                'trophy_rank'     => null,
                'wants_trophy'    => false,
                'is_labelled'     => false,
                'is_validated'    => true,
                'is_active'       => true,
                'is_public'       => true,
                'locale'          => 'fr',
                'contact_name'    => 'Isabelle Favre',
                'contact_email'   => 'rh@hes-so.ch',
                'rdv_url'         => null,
                'rdv_date'        => null,
            ],
            [
                'name'            => 'Clinique des Grangettes',
                'slug'            => 'grangettes',
                'type'            => 'sante',
                'primary_color'   => '#00AEEF',
                'secondary_color' => '#004B7F',
                'logo_url'        => null,
                'employee_count'  => 520,
                'trophy_rank'     => null,
                'wants_trophy'    => false,
                'is_labelled'     => false,
                'is_validated'    => true,
                'is_active'       => true,
                'is_public'       => true,
                'locale'          => 'fr',
                'contact_name'    => 'Dominique Ayer',
                'contact_email'   => 'rh@grangettes.ch',
                'rdv_url'         => null,
                'rdv_date'        => null,
            ],
            [
                'name'            => 'Manor',
                'slug'            => 'manor',
                'type'            => 'commerce',
                'primary_color'   => '#E10000',
                'secondary_color' => '#000000',
                'logo_url'        => 'https://logo.clearbit.com/manor.ch',
                'employee_count'  => 10000,
                'trophy_rank'     => null,
                'wants_trophy'    => false,
                'is_labelled'     => false,
                'is_validated'    => true,
                'is_active'       => true,
                'is_public'       => true,
                'locale'          => 'fr',
                'contact_name'    => 'Sandra Ritter',
                'contact_email'   => 'rh@manor.ch',
                'rdv_url'         => null,
                'rdv_date'        => null,
            ],

            // ================================================================
            // ÉTAT 5 — Non validées, en attente d'approbation
            // ================================================================

            [
                'name'            => 'Maus Frères',
                'slug'            => 'maus-freres',
                'type'            => 'commerce',
                'primary_color'   => '#333333',
                'secondary_color' => '#1A1A1A',
                'logo_url'        => null,
                'employee_count'  => 2100,
                'trophy_rank'     => null,
                'wants_trophy'    => false,
                'is_labelled'     => false,
                'is_validated'    => false,
                'is_active'       => false,
                'is_public'       => false,
                'locale'          => 'fr',
                'contact_name'    => 'Lucie Bernard',
                'contact_email'   => 'rh@maus.ch',
                'rdv_url'         => null,
                'rdv_date'        => null,
            ],
            [
                'name'            => 'Lonza',
                'slug'            => 'lonza',
                'type'            => 'industrie',
                'primary_color'   => '#0066CC',
                'secondary_color' => '#003366',
                'logo_url'        => 'https://logo.clearbit.com/lonza.com',
                'employee_count'  => 1800,
                'trophy_rank'     => null,
                'wants_trophy'    => false,
                'is_labelled'     => false,
                'is_validated'    => false,
                'is_active'       => false,
                'is_public'       => false,
                'locale'          => 'de',
                'contact_name'    => 'Andreas Wolf',
                'contact_email'   => 'hr@lonza.com',
                'rdv_url'         => null,
                'rdv_date'        => null,
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // Submissions & analytics
    // -------------------------------------------------------------------------

    private function seedSubmissionsAndAnalytics(array $entreprises): void
    {
        // [éligibles, non-éligibles] — 60 jours de données
        $quotas = [
            'bcge'          => [78, 24],
            'firmenich'     => [62, 19],
            'groupe-mutuel' => [48, 15],
            'sgs'           => [38, 12],
            'sig'           => [31, 10],
            'julius-baer'   => [45, 16],
            'axa-suisse'    => [38, 13],
            'logitech'      => [29, 10],
            'cern'          => [22, 8],
            'pictet'        => [19, 7],
            'richemont'     => [15, 5],
            'roche'         => [25, 9],
            'la-poste'      => [18, 6],
            'rolex'         => [12, 4],
            'migros-geneve' => [14, 5],
            'heig'          => [6,  2],
            'hes-so-ge'     => [9,  3],
            'grangettes'    => [7,  3],
            'manor'         => [11, 4],
        ];

        $submissions   = [];
        $analyticsRows = [];
        $base = now()->subDays(60);

        foreach ($quotas as $slug => [$eligible, $ineligible]) {
            if (! isset($entreprises[$slug])) continue;

            $eId  = $entreprises[$slug]['id'];
            $total = $eligible + $ineligible;

            for ($i = 0; $i < $total; $i++) {
                $token       = (string) Str::uuid();
                $isEligible  = $i < $eligible;
                $completedAt = (clone $base)->addSeconds(rand(0, 59 * 86400));

                $submissions[] = [
                    'session_token' => $token,
                    'entreprise_id' => $eId,
                    'is_eligible'   => $isEligible ? 1 : 0,
                    'completed_at'  => $completedAt,
                    'created_at'    => $completedAt,
                    'updated_at'    => $completedAt,
                ];

                $analyticsRows[] = $this->event(
                    'page_viewed', $eId, null,
                    ['referrer' => $this->randomReferrer(), 'device' => $this->randomDevice()],
                    $completedAt->copy()->subMinutes(rand(3, 15))
                );
                $analyticsRows[] = $this->event(
                    'quiz_started', $eId, $token, [],
                    $completedAt->copy()->subMinutes(rand(2, 12))
                );
                $analyticsRows[] = $this->event(
                    'quiz_completed', $eId, $token,
                    ['is_eligible' => $isEligible, 'duration_s' => rand(120, 480)],
                    $completedAt
                );

                if ($isEligible && rand(1, 10) <= 7) {
                    $analyticsRows[] = $this->event(
                        'rdv_clicked', $eId, $token, [],
                        $completedAt->copy()->addSeconds(rand(5, 60))
                    );
                }
            }

            // Abandons (~25 % du volume)
            $abandoned = (int) round($total * 0.25);
            for ($i = 0; $i < $abandoned; $i++) {
                $token = (string) Str::uuid();
                $at    = (clone $base)->addSeconds(rand(0, 59 * 86400));

                $analyticsRows[] = $this->event(
                    'page_viewed', $eId, null,
                    ['referrer' => $this->randomReferrer(), 'device' => $this->randomDevice()],
                    $at->copy()->subMinutes(rand(2, 8))
                );
                $analyticsRows[] = $this->event('quiz_started', $eId, $token, [], $at);
                $analyticsRows[] = $this->event(
                    'quiz_abandoned', $eId, $token,
                    [
                        'last_question_index' => rand(0, $this->quizQuestionCount - 1),
                        'total_questions'     => $this->quizQuestionCount,
                        'session_duration_s'  => rand(15, 200),
                    ],
                    $at->copy()->addSeconds(rand(30, 300))
                );
            }
        }

        foreach (array_chunk($submissions, 100) as $chunk) {
            DB::table('submissions')->insert($chunk);
        }
        foreach (array_chunk($analyticsRows, 200) as $chunk) {
            DB::table('analytics_events')->insert($chunk);
        }
    }

    private function event(string $type, ?int $entrepriseId, ?string $token, array $metadata, Carbon $at): array
    {
        return [
            'type'          => $type,
            'entreprise_id' => $entrepriseId,
            'session_token' => $token,
            'metadata'      => json_encode($metadata),
            'created_at'    => $at,
            'updated_at'    => $at,
        ];
    }

    private function randomReferrer(): string
    {
        return fake()->randomElement(['direct', 'linkedin.com', 'email', 'intranet', 'qrcode', 'google.com']);
    }

    private function randomDevice(): string
    {
        return fake()->randomElement(['mobile', 'mobile', 'desktop', 'desktop', 'tablet']);
    }
}
