<?php

namespace Database\Seeders;

use App\Models\AnalyticsEvent;
use App\Models\CampaignStats;
use App\Models\Entreprise;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = $this->seedUsers();
        $this->seedCampaignStats($admin->id);
        $entreprises = $this->seedEntreprises();
        $this->seedSubmissionsAndAnalytics($entreprises);
    }

    // -------------------------------------------------------------------------

    private function seedUsers(): User
    {
        $admin = User::factory()->create([
            'name'     => 'Admin HUG',
            'email'    => 'admin@hug-ge.ch',
            'is_admin' => true,
        ]);

        User::factory()->create([
            'name'     => 'Julie Dumont',
            'email'    => 'julie.dumont@hug-ge.ch',
            'is_admin' => true,
        ]);

        return $admin;
    }

    private function seedCampaignStats(int $updatedBy): void
    {
        CampaignStats::create([
            'donations_count'     => 214,
            'lives_saved'         => 642,
            'hug_hospitals_count' => 3,
            'updated_by'          => $updatedBy,
        ]);
    }

    private function seedEntreprises(): array
    {
        $data = [
            // Podium trophée
            [
                'name' => 'Banque Cantonale de Genève', 'slug' => 'bcge',
                'type' => 'banque', 'primary_color' => '#003F7D',
                'employee_count' => 850, 'trophy_rank' => 1,
                'is_labelled' => true, 'is_validated' => true, 'is_active' => true,
                'contact_name' => 'Marc Fontaine', 'contact_email' => 'rh@bcge.ch',
            ],
            [
                'name' => 'Firmenich', 'slug' => 'firmenich',
                'type' => 'industrie', 'primary_color' => '#00833E',
                'employee_count' => 3200, 'trophy_rank' => 2,
                'is_labelled' => true, 'is_validated' => true, 'is_active' => true,
                'contact_name' => 'Sophie Müller', 'contact_email' => 'wellbeing@firmenich.com',
            ],
            [
                'name' => 'Groupe Mutuel', 'slug' => 'groupe-mutuel',
                'type' => 'assurance', 'primary_color' => '#E30613',
                'employee_count' => 1400, 'trophy_rank' => 3,
                'is_labelled' => true, 'is_validated' => true, 'is_active' => true,
                'contact_name' => 'Alain Pernet', 'contact_email' => 'rh@groupemutuel.ch',
            ],
            // Classés (sans podium)
            [
                'name' => 'SGS', 'slug' => 'sgs',
                'type' => 'technologie', 'primary_color' => '#009FE3',
                'employee_count' => 960, 'trophy_rank' => 4,
                'is_labelled' => true, 'is_validated' => true, 'is_active' => true,
                'contact_name' => 'Céline Rochat', 'contact_email' => 'hr@sgs.com',
            ],
            [
                'name' => 'SIG — Services Industriels de Genève', 'slug' => 'sig',
                'type' => 'service', 'primary_color' => '#F7941D',
                'employee_count' => 1900, 'trophy_rank' => 5,
                'is_labelled' => true, 'is_validated' => true, 'is_active' => true,
                'contact_name' => 'Pierre-Alain Duc', 'contact_email' => 'rh@sig-ge.ch',
            ],
            // Labelisés sans rang
            [
                'name' => 'CERN', 'slug' => 'cern',
                'type' => 'technologie', 'primary_color' => '#0053A0',
                'employee_count' => 2500, 'trophy_rank' => null,
                'is_labelled' => true, 'is_validated' => true, 'is_active' => true,
                'contact_name' => 'Elena Kovač', 'contact_email' => 'hr@cern.ch',
            ],
            [
                'name' => 'Pictet & Cie', 'slug' => 'pictet',
                'type' => 'banque', 'primary_color' => '#1B4B8B',
                'employee_count' => 4700, 'trophy_rank' => null,
                'is_labelled' => true, 'is_validated' => true, 'is_active' => true,
                'contact_name' => 'Nathalie Clerc', 'contact_email' => 'rh@pictet.com',
            ],
            [
                'name' => 'Richemont', 'slug' => 'richemont',
                'type' => 'commerce', 'primary_color' => '#1A1A2E',
                'employee_count' => 6000, 'trophy_rank' => null,
                'is_labelled' => true, 'is_validated' => true, 'is_active' => true,
                'contact_name' => 'Thomas Bauer', 'contact_email' => 'hr@richemont.com',
            ],
            // Participantes validées, pas encore labelisées
            [
                'name' => 'HES-SO Genève', 'slug' => 'hes-so-ge',
                'type' => 'education', 'primary_color' => '#E30613',
                'employee_count' => 700, 'trophy_rank' => null,
                'is_labelled' => false, 'is_validated' => true, 'is_active' => true,
                'contact_name' => 'Isabelle Favre', 'contact_email' => 'rh@hes-so.ch',
            ],
            [
                'name' => 'Clinique des Grangettes', 'slug' => 'grangettes',
                'type' => 'sante', 'primary_color' => '#00AEEF',
                'employee_count' => 520, 'trophy_rank' => null,
                'is_labelled' => false, 'is_validated' => true, 'is_active' => true,
                'contact_name' => 'Dominique Ayer', 'contact_email' => 'rh@grangettes.ch',
            ],
            // En attente de validation (via formulaire inscription)
            [
                'name' => 'Maus Frères', 'slug' => 'maus-freres',
                'type' => 'commerce', 'primary_color' => '#333333',
                'employee_count' => 2100, 'trophy_rank' => null,
                'is_labelled' => false, 'is_validated' => false, 'is_active' => false,
                'contact_name' => 'Lucie Bernard', 'contact_email' => 'rh@maus.ch',
            ],
            [
                'name' => 'Lonza', 'slug' => 'lonza',
                'type' => 'industrie', 'primary_color' => '#0066CC',
                'employee_count' => 1800, 'trophy_rank' => null,
                'is_labelled' => false, 'is_validated' => false, 'is_active' => false,
                'contact_name' => 'Andreas Wolf', 'contact_email' => 'hr@lonza.com',
            ],
        ];

        $now = now();
        $rows = [];
        foreach ($data as $row) {
            $rows[] = array_merge($row, ['created_at' => $now, 'updated_at' => $now]);
        }
        DB::table('entreprises')->insert($rows);

        return Entreprise::where('is_active', true)->get()->keyBy('slug')->toArray();
    }

    private function seedSubmissionsAndAnalytics(array $entreprises): void
    {
        // Nombre de quiz complétés par entreprise [eligible, ineligible]
        $quotas = [
            'bcge'          => [52, 18],
            'firmenich'     => [44, 21],
            'groupe-mutuel' => [31, 14],
            'sgs'           => [27, 12],
            'sig'           => [23, 9],
            'cern'          => [19, 8],
            'pictet'        => [15, 6],
            'richemont'     => [11, 5],
            'hes-so-ge'     => [8, 3],
            'grangettes'    => [6, 2],
        ];

        $submissions = [];
        $analyticsRows = [];
        $base = now()->subDays(28);

        foreach ($quotas as $slug => [$eligible, $ineligible]) {
            if (! isset($entreprises[$slug])) continue;
            $eId = $entreprises[$slug]['id'];
            $total = $eligible + $ineligible;

            for ($i = 0; $i < $total; $i++) {
                $token      = (string) Str::uuid();
                $isEligible = $i < $eligible;
                $offset     = rand(0, 27 * 86400);
                $completedAt = (clone $base)->addSeconds($offset);

                $submissions[] = [
                    'session_token' => $token,
                    'entreprise_id' => $eId,
                    'is_eligible'   => $isEligible ? 1 : 0,
                    'completed_at'  => $completedAt,
                    'created_at'    => $completedAt,
                    'updated_at'    => $completedAt,
                ];

                // page_viewed
                $analyticsRows[] = $this->event('page_viewed', $eId, null,
                    ['referrer' => $this->randomReferrer(), 'device' => $this->randomDevice()],
                    $completedAt->copy()->subMinutes(rand(3, 15))
                );
                // quiz_started
                $analyticsRows[] = $this->event('quiz_started', $eId, $token, [],
                    $completedAt->copy()->subMinutes(rand(2, 12))
                );
                // quiz_completed
                $durationS = rand(120, 480);
                $analyticsRows[] = $this->event('quiz_completed', $eId, $token,
                    ['is_eligible' => $isEligible, 'duration_s' => $durationS],
                    $completedAt
                );
                // rdv_clicked for eligible (70% taux de clic)
                if ($isEligible && rand(1, 10) <= 7) {
                    $analyticsRows[] = $this->event('rdv_clicked', $eId, $token, [],
                        $completedAt->copy()->addSeconds(rand(5, 60))
                    );
                }
            }

            // Abandons (env. 20% du trafic en plus)
            $abandoned = (int) round($total * 0.2);
            for ($i = 0; $i < $abandoned; $i++) {
                $token  = (string) Str::uuid();
                $offset = rand(0, 27 * 86400);
                $at     = (clone $base)->addSeconds($offset);
                $lastQ  = rand(0, 10);

                $analyticsRows[] = $this->event('page_viewed', $eId, null,
                    ['referrer' => $this->randomReferrer(), 'device' => $this->randomDevice()],
                    $at->copy()->subMinutes(rand(2, 8))
                );
                $analyticsRows[] = $this->event('quiz_started', $eId, $token, [], $at);
                $analyticsRows[] = $this->event('quiz_abandoned', $eId, $token,
                    ['last_question_index' => $lastQ, 'total_questions' => 20, 'session_duration_s' => rand(15, 200)],
                    $at->copy()->addSeconds(rand(30, 300))
                );
            }
        }

        // Bulk insert
        foreach (array_chunk($submissions, 100) as $chunk) {
            DB::table('submissions')->insert($chunk);
        }
        foreach (array_chunk($analyticsRows, 200) as $chunk) {
            DB::table('analytics_events')->insert($chunk);
        }
    }

    // -------------------------------------------------------------------------

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
        return fake()->randomElement([
            'direct', 'linkedin.com', 'email', 'intranet', 'qrcode', 'google.com',
        ]);
    }

    private function randomDevice(): string
    {
        return fake()->randomElement(['mobile', 'mobile', 'desktop', 'desktop', 'tablet']);
    }
}
