<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CampaignStatsResource;
use App\Models\CampaignStats;
use Illuminate\Http\Request;

class CampaignStatsController extends Controller
{
    public function show(): CampaignStatsResource
    {
        return new CampaignStatsResource(
            CampaignStats::getInstance()->load('updatedBy')
        );
    }

    public function update(Request $request): CampaignStatsResource
    {
        $data = $request->validate([
            'donations_count'     => 'required|integer|min:0',
            'lives_saved'         => 'required|integer|min:0',
            'hug_hospitals_count' => 'required|integer|min:0',
        ]);

        $stats = CampaignStats::getInstance();
        $stats->update(array_merge($data, ['updated_by' => $request->user()->id]));

        return new CampaignStatsResource($stats->load('updatedBy'));
    }
}
