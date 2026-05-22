<?php

namespace App\Http\Resources;

use App\Models\Entreprise;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignStatsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'donations_count'     => $this->donations_count,
            'lives_saved'         => $this->lives_saved,
            'hug_hospitals_count' => $this->hug_hospitals_count,
            'eligible_count'      => Submission::where('is_eligible', true)->count(),
            'entreprises_count'   => Entreprise::where('is_active', true)->count(),
            'labelled_count'      => Entreprise::where('is_labelled', true)->count(),
            'updated_at'          => $this->updated_at?->toIso8601String(),
            'updated_by'          => new UserResource($this->whenLoaded('updatedBy')),
        ];
    }
}
