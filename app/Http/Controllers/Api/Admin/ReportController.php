<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\Entreprise;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $request->validate([
            'entreprise_id' => 'required|exists:entreprises,id',
        ]);

        $entreprise = Entreprise::findOrFail($request->integer('entreprise_id'));

        $submissions = Submission::where('entreprise_id', $entreprise->id);
        $total       = $submissions->clone()->count();
        $eligible    = $submissions->clone()->where('is_eligible', true)->count();
        $ineligible  = $submissions->clone()->where('is_eligible', false)->count();

        $quizStarted   = AnalyticsEvent::where('entreprise_id', $entreprise->id)->where('type', 'quiz_started')->count();
        $quizCompleted = AnalyticsEvent::where('entreprise_id', $entreprise->id)->where('type', 'quiz_completed')->count();
        $rdvClicked    = AnalyticsEvent::where('entreprise_id', $entreprise->id)->where('type', 'rdv_clicked')->count();

        $avgDuration = AnalyticsEvent::where('entreprise_id', $entreprise->id)
            ->where('type', 'quiz_completed')
            ->whereNotNull('metadata')
            ->avg(DB::raw('JSON_UNQUOTE(JSON_EXTRACT(metadata, "$.duration_s"))'));

        $abandonByQuestion = AnalyticsEvent::where('entreprise_id', $entreprise->id)
            ->where('type', 'quiz_abandoned')
            ->whereNotNull('metadata')
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(metadata, "$.last_question_index")) as q_index, COUNT(*) as total')
            ->groupBy('q_index')
            ->pluck('total', 'q_index');

        return response()->json([
            'entreprise' => [
                'id'             => $entreprise->id,
                'name'           => $entreprise->name,
                'slug'           => $entreprise->slug,
                'employee_count' => $entreprise->employee_count,
                'contact_name'   => $entreprise->contact_name,
                'contact_email'  => $entreprise->contact_email,
            ],
            'participation' => [
                'quiz_started'       => $quizStarted,
                'quiz_completed'     => $quizCompleted,
                'total_submissions'  => $total,
                'eligible'           => $eligible,
                'ineligible'         => $ineligible,
                'rdv_clicked'        => $rdvClicked,
                'participation_rate' => $entreprise->employee_count > 0
                    ? round($quizStarted / $entreprise->employee_count, 4)
                    : null,
                'eligibility_rate'   => $total > 0 ? round($eligible / $total, 4) : null,
                'conversion_rate'    => $eligible > 0 ? round($rdvClicked / $eligible, 4) : null,
            ],
            'behavior' => [
                'avg_duration_s'      => $avgDuration ? round($avgDuration) : null,
                'abandon_by_question' => $abandonByQuestion,
            ],
            // TODO: générer un PDF avec barryvdh/laravel-dompdf
            '_note' => 'Export PDF à implémenter avec barryvdh/laravel-dompdf',
        ]);
    }
}
