<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'is_eligible'  => $this->is_eligible,
            'completed_at' => $this->completed_at?->toIso8601String(),
            'created_at'   => $this->created_at->toIso8601String(),
            'entreprise'   => $this->whenLoaded('entreprise', fn() => [
                'id'   => $this->entreprise->id,
                'name' => $this->entreprise->name,
                'slug' => $this->entreprise->slug,
            ]),
        ];
    }
}
