<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\Entreprise;
use App\Models\Submission;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

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
            ->pluck('total', 'q_index');

        $entrepriseData = [
            'id'             => $entreprise->id,
            'name'           => $entreprise->name,
            'slug'           => $entreprise->slug,
            'employee_count' => $entreprise->employee_count,
            'contact_name'   => $entreprise->contact_name,
            'contact_email'  => $entreprise->contact_email,
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
            $pdf = Pdf::loadView('pdf.report', [
                'entreprise'    => $entrepriseData,
                'participation' => $participationData,
                'behavior'      => $behaviorData,
                'generatedAt'   => now()->format('d.m.Y'),
                'tr'            => trans('pdf'),
                'logoSrc'       => $this->svgToPngDataUri(
                    public_path('images/hug-logo.svg')
                ),
            ])->setPaper('a4', 'portrait');

            return $pdf->download("rapport-{$entreprise->slug}.pdf");
        }

        return response()->json([
            'entreprise'    => $entrepriseData,
            'participation' => $participationData,
            'behavior'      => $behaviorData,
        ]);
    }

    /**
     * Rasterise an SVG to PNG via Imagick and return a base64 data URI.
     * DomPDF handles PNG img tags reliably; inline SVG with CSS classes does not work.
     */
    private function svgToPngDataUri(string $path): string
    {
        $imagick = new \Imagick();
        $imagick->setBackgroundColor(new \ImagickPixel('transparent'));
        $imagick->setResolution(144, 144);
        $imagick->readImageBlob(file_get_contents($path));
        $imagick->setImageFormat('png32');
        $imagick->resizeImage(300, 0, \Imagick::FILTER_LANCZOS, 1);

        return 'data:image/png;base64,' . base64_encode($imagick->getImageBlob());
    }
}
