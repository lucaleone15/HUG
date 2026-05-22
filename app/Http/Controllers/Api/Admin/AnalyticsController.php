<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $entrepriseId = $request->integer('entreprise_id') ?: null;

        $base = fn() => AnalyticsEvent::when($entrepriseId, fn($q) => $q->where('entreprise_id', $entrepriseId));

        $funnel = collect(AnalyticsEvent::TYPES)
            ->mapWithKeys(fn($type) => [
                $type => $base()->where('type', $type)->count(),
            ]);

        $avgDuration = $base()
            ->where('type', 'quiz_completed')
            ->whereNotNull('metadata')
            ->avg(DB::raw('JSON_UNQUOTE(JSON_EXTRACT(metadata, "$.duration_s"))'));

        $abandonByQuestion = $base()
            ->where('type', 'quiz_abandoned')
            ->whereNotNull('metadata')
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(metadata, "$.last_question_index")) as q_index, COUNT(*) as total')
            ->groupBy('q_index')
            ->orderBy(DB::raw('CAST(q_index AS UNSIGNED)'))
            ->pluck('total', 'q_index');

        $byReferrer = $base()
            ->where('type', 'page_viewed')
            ->whereNotNull('metadata')
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(metadata, "$.referrer")) as referrer, COUNT(*) as total')
            ->groupByRaw('JSON_UNQUOTE(JSON_EXTRACT(metadata, "$.referrer"))')
            ->orderByDesc('total')
            ->pluck('total', 'referrer');

        $byDevice = $base()
            ->where('type', 'page_viewed')
            ->whereNotNull('metadata')
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(metadata, "$.device")) as device, COUNT(*) as total')
            ->groupByRaw('JSON_UNQUOTE(JSON_EXTRACT(metadata, "$.device"))')
            ->orderByDesc('total')
            ->pluck('total', 'device');

        $dailyActivity = $base()
            ->whereIn('type', ['quiz_started', 'quiz_completed'])
            ->selectRaw('DATE(created_at) as date, type, COUNT(*) as total')
            ->groupByRaw('DATE(created_at), type')
            ->orderBy('date')
            ->get()
            ->groupBy('date')
            ->map(fn($rows) => $rows->pluck('total', 'type'));

        return response()->json([
            'funnel'              => $funnel,
            'avg_duration_s'      => $avgDuration ? round($avgDuration) : null,
            'abandon_by_question' => $abandonByQuestion,
            'by_referrer'         => $byReferrer,
            'by_device'           => $byDevice,
            'daily_activity'      => $dailyActivity,
        ]);
    }
}
