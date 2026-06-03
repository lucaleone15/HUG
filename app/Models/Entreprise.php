<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entreprise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo_url',
        'primary_color',
        'secondary_color',
        'contact_name',
        'contact_email',
        'employee_count',
        'access_token',
        'is_active',
        'is_labelled',
        'is_validated',
        'is_public',
        'trophy_rank',
        'wants_trophy',
        'rdv_url',
        'rdv_date',
        'type',
        'locale'
    ];

    protected $casts = [
        'primary_color' => 'string',
        'secondary_color' => 'string',
        'employee_count' => 'integer',
        'is_active' => 'boolean',
        'is_labelled' => 'boolean',
        'is_validated' => 'boolean',
        'is_public' => 'boolean',
        'trophy_rank' => 'integer',
        'wants_trophy' => 'boolean',
        'rdv_date' => 'date:Y-m-d',
        'type' => 'string',
    ];

    /**
     * Vérifie si l'entreprise participe au trophée.
     * trophy_rank > 0 signifie participante.
     * trophy_rank <= 0 ou null = ne participe pas.
     */
    public function participatesInTrophy(): bool
    {
        return $this->trophy_rank !== null && $this->trophy_rank > 0;
    }

    /**
     * Vérifie si l'entreprise est un lauréat (top 3).
     */
    public function isLauréat(): bool
    {
        return in_array($this->trophy_rank, [1, 2, 3]);
    }

    /**
     * Récupère le nom du trophée selon le classement.
     */
    public function getTrophyNameAttribute(): ?string
    {
        return match ($this->trophy_rank) {
            1 => 'Or',
            2 => 'Argent',
            3 => 'Bronze',
            default => null,
        };
    }

    public function getRouteKeyName(): string
    {
        return 'access_token';
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function analyticsEvents(): HasMany
    {
        return $this->hasMany(AnalyticsEvent::class);
    }
}
