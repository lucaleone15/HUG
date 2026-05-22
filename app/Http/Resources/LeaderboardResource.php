<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaderboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $eligible       = (int) ($this->eligible_count ?? 0);
        $employeeCount  = $this->employee_count ?? 0;

        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'slug'               => $this->slug,
            'logo_url'           => $this->logo_url,
            'primary_color'      => $this->primary_color,
            'trophy_rank'        => $this->trophy_rank,
            'employee_count'     => $employeeCount,
            'eligible_count'     => $eligible,
            'participation_rate' => $employeeCount > 0
                ? round($eligible / $employeeCount, 4)
                : null,
        ];
    }
}
