<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class AdminEntrepriseResource extends EntrepriseResource
{
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'contact_name'     => $this->contact_name,
            'contact_email'    => $this->contact_email,
            'wants_trophy'     => $this->wants_trophy,
            'rdv_url'          => $this->rdv_url,
            'rdv_date'         => $this->rdv_date?->format('Y-m-d'),
            'eligible_count'   => $this->eligible_count   ?? null,
            'submission_count' => $this->submission_count ?? null,
        ]);
    }
}
