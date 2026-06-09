<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\Collecte;
use App\Models\Entreprise;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        
        $pageViewed    = AnalyticsEvent::where('type', 'page_viewed')->count();
        $quizStarted   = AnalyticsEvent::where('type', 'quiz_started')->count();
        $quizCompleted = AnalyticsEvent::where('type', 'quiz_completed')->count();
        $rdvClicked    = AnalyticsEvent::where('type', 'rdv_clicked')->count();
        $eligible      = Submission::where('is_eligible', true)->count();

        
        $avgDuration = AnalyticsEvent::where('type', 'quiz_completed')
            ->whereNotNull('metadata')
            ->avg(DB::raw('JSON_UNQUOTE(JSON_EXTRACT(metadata, "$.duration_s"))'));

        $abandonByQuestion = AnalyticsEvent::where('type', 'quiz_abandoned')
            ->whereNotNull('metadata')
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(metadata, "$.last_question_index")) as q_index, COUNT(*) as total')
            ->groupBy('q_index')
            ->orderBy(DB::raw('CAST(q_index AS UNSIGNED)'))
            ->pluck('total', 'q_index');

        $byReferrer = AnalyticsEvent::where('type', 'page_viewed')
            ->whereNotNull('metadata')
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(metadata, "$.referrer")) as referrer, COUNT(*) as total')
            ->groupByRaw('JSON_UNQUOTE(JSON_EXTRACT(metadata, "$.referrer"))')
            ->pluck('total', 'referrer');

        $byDevice = AnalyticsEvent::where('type', 'page_viewed')
            ->whereNotNull('metadata')
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(metadata, "$.device")) as device, COUNT(*) as total')
            ->groupByRaw('JSON_UNQUOTE(JSON_EXTRACT(metadata, "$.device"))')
            ->pluck('total', 'device');

      
        $collectes2026 = Collecte::whereYear('rdv_date', now()->year)->count();
        $wantsTrophy   = Entreprise::where('is_active', true)->where('wants_trophy', true)->count();

        $perEntreprise = Entreprise::where('is_active', true)
            ->withCount([
                'submissions as submission_count',
                'submissions as eligible_count' => fn($q) => $q->where('is_eligible', true),
            ])
            ->get()
            ->map(fn($e) => [
                'id'                 => $e->id,
                'name'               => $e->name,
                'slug'               => $e->slug,
                'employee_count'     => $e->employee_count,
                'submission_count'   => $e->submission_count,
                'eligible_count'     => $e->eligible_count,
                'eligibility_rate'   => $e->submission_count > 0
                    ? round($e->eligible_count / $e->submission_count, 4)
                    : null,
                'participation_rate' => $e->employee_count > 0
                    ? round($e->submission_count / $e->employee_count, 4)
                    : null,
            ])
            ->sortByDesc('eligible_count')
            ->values();

        return response()->json([
            'funnel' => [
                'page_viewed'    => $pageViewed,
                'quiz_started'   => $quizStarted,
                'quiz_completed' => $quizCompleted,
                'eligible'       => $eligible,
                'rdv_clicked'    => $rdvClicked,
            ],
            'rates' => [
                'completion_rate'  => $quizStarted > 0  ? round($quizCompleted / $quizStarted, 4)  : null,
                'eligibility_rate' => $quizCompleted > 0 ? round($eligible / $quizCompleted, 4)      : null,
                'conversion_rate'  => $eligible > 0      ? round($rdvClicked / $eligible, 4)         : null,
            ],
            'behavior' => [
                'avg_duration_s'        => $avgDuration ? round($avgDuration) : null,
                'abandon_by_question'   => $abandonByQuestion,
                'by_referrer'           => $byReferrer,
                'by_device'             => $byDevice,
            ],
            'by_entreprise'  => $perEntreprise,
            'collectes_year' => $collectes2026,
            'wants_trophy'   => $wantsTrophy,
        ]);
    }
}
