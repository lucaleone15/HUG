<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'employee_count' => 'integer',
        'is_active' => 'boolean',
        'is_labelled' => 'boolean',
        'is_validated' => 'boolean',
        'is_public' => 'boolean',
        'trophy_rank' => 'integer',
        'wants_trophy' => 'boolean',
        'rdv_date' => 'date:Y-m-d',
    ];

    public function getRouteKeyName(): string
    {
        return 'access_token';
    }

    public function collectes(): HasMany
    {
        return $this->hasMany(Collecte::class);
    }

    public function activeCollecte(): HasOne
    {
        return $this->hasOne(Collecte::class)->where('is_active', true)->latestOfMany();
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }


}
