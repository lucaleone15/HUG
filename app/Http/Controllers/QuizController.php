<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use App\Models\Submission;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class QuizController extends Controller
{
    /** Champs jamais exposés au front — invariant critique */
    private const SENSITIVE_FIELDS = ['is_disqualifying', 'disqualification_reason'];

    // -------------------------------------------------------------------------
    // Actions publiques
    // -------------------------------------------------------------------------

    public function show(Entreprise $entreprise): View
    {
        abort_if(! $entreprise->is_active, 404);

        $token = (string) Str::uuid();
        session(['quiz_token' => $token]);

        $questions = collect($this->loadQuiz()['questions'])
            ->map(fn ($q) => $this->stripSensitiveFields($q))
            ->all();

        return view('quiz.show', compact('entreprise', 'questions'));
    }

    public function store(Request $request, Entreprise $entreprise): RedirectResponse
    {
        abort_if(! $entreprise->is_active, 404);

        $token = session('quiz_token');
        abort_if(! $token, 422);

        $answers   = $request->input('answers', []);
        $questions = $this->loadQuiz()['questions'];

        // Les réponses travel_check et birth_check arrivent JSON-encodées depuis le form HTML.
        // Les réponses checklist arrivent sous forme de tableau PHP natif (answers[qN][]).
        // Les réponses yes_no arrivent comme strings — inchangées.
        foreach ($answers as &$val) {
            if (is_string($val) && str_starts_with(ltrim($val), '{')) {
                $decoded = json_decode($val, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $val = $decoded;
                }
            }
        }
        unset($val);

        // Questions actives : celles dont les conditions de dépendance sont remplies
        $activeQuestions = collect($questions)->filter(
            fn ($q) => $this->conditionsMet($q, $answers)
        );

        // Toutes les questions actives doivent avoir une réponse non-vide
        $unanswered = $activeQuestions->filter(
            fn ($q) => ! $this->hasValidAnswer($answers, $q)
        );
        if ($unanswered->isNotEmpty()) {
            return back()->withErrors(['answers' => 'Toutes les questions doivent être répondues.']);
        }

        // Calcul d'éligibilité — uniquement côté serveur, jamais côté Vue
        $isEligible             = true;
        $needsEvaluation        = false;
        $disqualificationReasons = [];

        foreach ($activeQuestions as $question) {
            $result = $this->evaluateQuestion($question, $answers[$question['id']]);

            if ($result['is_disqualifying']) {
                $isEligible = false;
                array_push($disqualificationReasons, ...$result['reasons']);
            }

            if ($result['needs_evaluation'] ?? false) {
                $needsEvaluation = true;
            }
        }

        // firstOrCreate : protection contre le double-submit (unique sur session_token)
        Submission::firstOrCreate(
            ['session_token' => $token],
            [
                'entreprise_id' => $entreprise->id,
                'is_eligible'   => $isEligible,
                'completed_at'  => now(),
            ]
        );

        // Flash les raisons vers la result page (1 seul redirect, non persisté en DB)
        if (! $isEligible) {
            session()->flash(
                'disqualification_reasons',
                array_values(array_unique($disqualificationReasons))
            );
        }
        if ($needsEvaluation) {
            session()->flash('needs_evaluation', true);
        }

        return redirect()->route('quiz.result', $entreprise);
    }

    public function result(Entreprise $entreprise): View
    {
        $token = session('quiz_token');
        abort_if(! $token, 404);

        $submission = Submission::where('session_token', $token)->firstOrFail();

        // Lire les flash avant de nettoyer la session
        $disqualificationReasons = session('disqualification_reasons', []);
        $needsEvaluation         = session('needs_evaluation', false);

        session()->forget('quiz_token');

        return view('quiz.result', compact(
            'entreprise',
            'submission',
            'disqualificationReasons',
            'needsEvaluation'
        ));
    }

    // -------------------------------------------------------------------------
    // Chargement du quiz
    // -------------------------------------------------------------------------

    private function loadQuiz(): array
    {
        $locale = app()->getLocale();
        $path   = resource_path("quiz/quiz.{$locale}.json");

        if (! file_exists($path)) {
            $path = resource_path('quiz/quiz.json');
        }

        return Cache::rememberForever("quiz_{$locale}", fn () =>
            json_decode(file_get_contents($path), true)
        );
    }

    // -------------------------------------------------------------------------
    // Stripping — show() uniquement
    // INVARIANT : is_disqualifying et disqualification_reason ne quittent jamais le serveur
    // -------------------------------------------------------------------------

    private function stripSensitiveFields(array $question): array
    {
        $strip = array_flip(self::SENSITIVE_FIELDS);

        // options (yes_no, et option "aucun" des checklists)
        if (isset($question['options'])) {
            $question['options'] = collect($question['options'])
                ->map(fn ($opt) => array_diff_key($opt, $strip))
                ->values()
                ->all();
        }

        // items (checklist, birth_check)
        if (isset($question['items'])) {
            $question['items'] = collect($question['items'])
                ->map(fn ($item) => array_diff_key($item, $strip))
                ->values()
                ->all();
        }

        // risk_map (travel_check, birth_check) — on garde diseases et waiting_period_months pour l'UX
        if (isset($question['risk_map'])) {
            $question['risk_map'] = collect($question['risk_map'])
                ->map(fn ($zone) => array_diff_key($zone, $strip))
                ->all();
        }

        // Champs de documentation interne — inutiles au front
        unset($question['data_schema'], $question['types_note']);

        return $question;
    }

    // -------------------------------------------------------------------------
    // Conditions de dépendance entre questions
    // -------------------------------------------------------------------------

    private function conditionsMet(array $question, array $answers): bool
    {
        foreach ($question['conditions'] as $cond) {
            if (($answers[$cond['depends_on']] ?? null) !== $cond['expects']) {
                return false;
            }
        }
        return true;
    }

    // -------------------------------------------------------------------------
    // Validation de présence de réponse
    // -------------------------------------------------------------------------

    private function hasValidAnswer(array $answers, array $question): bool
    {
        $answer = $answers[$question['id']] ?? null;

        if ($answer === null) {
            return false;
        }

        // Tableau vide = question non répondue
        if (is_array($answer) && empty($answer)) {
            return false;
        }

        // travel_check spécifique : trips doit être non-vide OU option "non" choisie
        if (($question['type'] ?? '') === 'travel_check') {
            if (is_array($answer) && empty($answer['trips'] ?? [])) {
                return false;
            }
        }

        return true;
    }

    // -------------------------------------------------------------------------
    // Évaluation de l'éligibilité — dispatch par type
    // Retourne : ['is_disqualifying' => bool, 'reasons' => string[], 'needs_evaluation' => bool?]
    // -------------------------------------------------------------------------

    private function evaluateQuestion(array $question, mixed $answer): array
    {
        return match ($question['type'] ?? 'yes_no') {
            'checklist'    => $this->evaluateChecklist($question, $answer),
            'travel_check' => $this->evaluateTravelCheck($question, $answer),
            'birth_check'  => $this->evaluateBirthCheck($question, $answer),
            default        => $this->evaluateYesNo($question, $answer),
        };
    }

    /**
     * yes_no — answer : string (ID de l'option choisie)
     */
    private function evaluateYesNo(array $question, mixed $answer): array
    {
        $option = collect($question['options'])->firstWhere('id', $answer);
        $isDisq = $option['is_disqualifying'] ?? false;

        return [
            'is_disqualifying' => $isDisq,
            'reasons'          => ($isDisq && ! empty($option['disqualification_reason']))
                ? [$option['disqualification_reason']]
                : [],
        ];
    }

    /**
     * checklist — answer :
     *   - string  → ID de l'option "aucun" (non disqualifiant)
     *   - string[] → IDs des items sélectionnés
     */
    private function evaluateChecklist(array $question, mixed $answer): array
    {
        // Option "aucun" sélectionnée
        $noneOptionIds = collect($question['options'] ?? [])->pluck('id')->all();
        if (is_string($answer) && in_array($answer, $noneOptionIds, true)) {
            return ['is_disqualifying' => false, 'reasons' => []];
        }

        $selectedIds = is_array($answer) ? $answer : [$answer];
        $reasons     = [];
        $isDisq      = false;

        foreach ($selectedIds as $id) {
            $item = collect($question['items'] ?? [])->firstWhere('id', $id);
            if (! $item) {
                continue;
            }
            if ($item['is_disqualifying'] ?? false) {
                $isDisq = true;
                if (! empty($item['disqualification_reason'])) {
                    $reasons[] = $item['disqualification_reason'];
                }
            }
        }

        return ['is_disqualifying' => $isDisq, 'reasons' => $reasons];
    }

    /**
     * travel_check — answer :
     *   - string → ID de l'option "pas de voyage" (non disqualifiant)
     *   - array  → ['trips' => [['region' => string, 'return_date' => 'YYYY-MM-DD'], ...]]
     *
     * Logique : pour chaque voyage, si la région est dans risk_map ET le retour
     * date de moins de waiting_period_months mois → disqualifiant.
     */
    private function evaluateTravelCheck(array $question, mixed $answer): array
    {
        if (is_string($answer)) {
            return ['is_disqualifying' => false, 'reasons' => []];
        }

        $trips   = $answer['trips'] ?? [];
        $riskMap = $question['risk_map'] ?? [];
        $reasons = [];
        $isDisq  = false;

        foreach ($trips as $trip) {
            $region = $trip['region'] ?? null;
            if (! $region || ! isset($riskMap[$region])) {
                continue;
            }

            $zone = $riskMap[$region];
            if (! ($zone['is_disqualifying'] ?? false)) {
                continue;
            }

            $waitingMonths = $zone['waiting_period_months'] ?? 6;
            $returnDate    = $trip['return_date'] ?? null;

            // Sans date de retour renseignée → voyage considéré récent → disqualifiant
            $withinWindow = $returnDate
                ? Carbon::parse($returnDate)->diffInMonths(now()) < $waitingMonths
                : true;

            if ($withinWindow) {
                $isDisq = true;
                if (! empty($zone['disqualification_reason'])) {
                    $reasons[] = $zone['disqualification_reason'];
                }
            }
        }

        return ['is_disqualifying' => $isDisq, 'reasons' => array_unique($reasons)];
    }

    /**
     * birth_check — pas directement disqualifiant.
     * Positionne needs_evaluation = true sur la Submission si un item est sélectionné.
     *
     * answer :
     *   - string → ID de l'option "aucune de ces situations"
     *   - array  → ['items' => string[], 'countries' => string[]]
     */
    private function evaluateBirthCheck(array $question, mixed $answer): array
    {
        if (is_string($answer)) {
            return ['is_disqualifying' => false, 'needs_evaluation' => false, 'reasons' => []];
        }

        return [
            'is_disqualifying' => false,
            'needs_evaluation' => count($answer['items'] ?? []) > 0,
            'reasons'          => [],
        ];
    }
}
