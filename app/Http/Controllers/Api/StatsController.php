<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CampaignStatsResource;
use App\Models\CampaignStats;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function index(): JsonResponse
    {
        $stats = CampaignStats::getInstance()->load('updatedBy');

        return response()->json(new CampaignStatsResource($stats));
    }
}
