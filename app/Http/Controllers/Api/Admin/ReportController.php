<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\Entreprise;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;

class ReportController extends Controller
{
    public function show(Request $request): JsonResponse|\Illuminate\Http\Response
    {
        $request->validate([
            'entreprise_id' => 'required|exists:entreprises,id',
            'locale'        => 'nullable|in:fr,de,it,en',
        ]);

        $locale = $request->input('locale', 'fr');
        App::setLocale($locale);

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
            ->pluck('total', 'q_index')
            ->filter(fn($_, $k) => is_numeric($k) && $k !== null)
            ->sortKeys()
            ->toArray();

        $entrepriseData = [
            'id'             => $entreprise->id,
            'name'           => $entreprise->name,
            'slug'           => $entreprise->slug,
            'employee_count' => $entreprise->employee_count,
            'contact_name'   => $entreprise->contact_name,
            'contact_email'  => $entreprise->contact_email,
            'logo_url'       => $entreprise->logo_url,
        ];

        $participationData = [
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
        ];

        $behaviorData = [
            'avg_duration_s'      => $avgDuration ? round($avgDuration) : null,
            'abandon_by_question' => $abandonByQuestion,
        ];

        if ($request->format === 'pdf') {
            set_time_limit(120);

            $html = view('pdf.report-preview', [
                'entreprise'    => $entrepriseData,
                'participation' => $participationData,
                'behavior'      => $behaviorData,
                'generatedAt'   => now()->locale($locale)->isoFormat('D MMMM YYYY'),
                'tr'            => trans('pdf'),
            ])->render();

            $pdf = Browsershot::html($html)
                ->setChromePath('/Applications/Google Chrome.app/Contents/MacOS/Google Chrome')
                ->setNodeModulePath(base_path('node_modules'))
                ->windowSize(794, 1122)
                ->format('A4')
                ->noSandbox()
                ->showBackground()
                ->margins(0, 0, 0, 0)
                ->pages('1')
                ->pdf();

            return response($pdf, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => "attachment; filename=\"rapport-{$entreprise->slug}.pdf\"",
            ]);
        }

        return response()->json([
            'entreprise'    => $entrepriseData,
            'participation' => $participationData,
            'behavior'      => $behaviorData,
        ]);
    }
}
