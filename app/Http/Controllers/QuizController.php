<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function show(Entreprise $entreprise): View
    {
        abort_if(! $entreprise->is_active, 404);

        $token = (string) Str::uuid();
        session(['quiz_token' => $token]);

        $questions = collect($this->loadQuiz()['questions'])
            ->map(function ($question) {
                $question['options'] = collect($question['options'])
                    ->map(fn($opt) => array_diff_key($opt, ['is_disqualifying' => null]))
                    ->values()
                    ->all();
                return $question;
            })
            ->all();

        return view('quiz.show', compact('entreprise', 'questions'));
    }

    public function store(Request $request, Entreprise $entreprise): RedirectResponse
    {
        abort_if(! $entreprise->is_active, 404);

        $token = session('quiz_token');
        abort_if(! $token, 422);

        $answers = $request->input('answers', []);
        $questions = $this->loadQuiz()['questions'];

        // Valider que toutes les questions actives (conditions remplies) ont une réponse
        $activeQuestions = collect($questions)->filter(
            fn($q) => $this->conditionsMet($q, $answers)
        );

        $unanswered = $activeQuestions->filter(fn($q) => ! isset($answers[$q['id']]));
        if ($unanswered->isNotEmpty()) {
            return back()->withErrors(['answers' => 'Toutes les questions doivent être répondues.']);
        }

        // Calcul éligibilité — uniquement côté serveur, jamais côté Vue
        $isEligible = $activeQuestions->every(function ($q) use ($answers) {
            $chosen = $answers[$q['id']] ?? null;
            return collect($q['options'])
                ->where('id', $chosen)
                ->where('is_disqualifying', false)
                ->isNotEmpty();
        });

        Submission::create([
            'session_token' => $token,
            'entreprise_id' => $entreprise->id,
            'is_eligible'   => $isEligible,
            'completed_at'  => now(),
        ]);

        return redirect()->route('quiz.result', $entreprise);
    }

    public function result(Entreprise $entreprise): View
    {
        $token = session('quiz_token');
        abort_if(! $token, 404);

        $submission = Submission::where('session_token', $token)->firstOrFail();

        session()->forget('quiz_token');

        return view('quiz.result', compact('entreprise', 'submission'));
    }

    private function loadQuiz(): array
    {
        return Cache::rememberForever('quiz', fn() =>
            json_decode(file_get_contents(resource_path('quiz/quiz.json')), true)
        );
    }

    private function conditionsMet(array $question, array $answers): bool
    {
        foreach ($question['conditions'] as $cond) {
            if (($answers[$cond['depends_on']] ?? null) !== $cond['expects']) {
                return false;
            }
        }
        return true;
    }
}
