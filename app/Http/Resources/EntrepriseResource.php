<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntrepriseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'slug'            => $this->slug,
            'logo_url'        => $this->logo_url,
            'primary_color'   => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'employee_count'  => $this->employee_count,
            'is_labelled'     => $this->is_labelled,
            'is_validated'    => $this->is_validated,
            'is_active'       => $this->is_active,
            'trophy_rank'     => $this->trophy_rank,
            'type'            => $this->type,
            // contact_name / contact_email volontairement exclus (données internes)
        ];
    }
}
