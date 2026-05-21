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
        'is_active',
        'is_labelled',
        'trophy_rank'
    ];

    protected $casts = [
        'primary_color' => 'string',
        'secondary_color' => 'string',
        'employee_count' => 'integer',
        'is_active' => 'boolean',
        'is_labelled' => 'boolean',
        'trophy_rank' => 'integer',
    ];

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function analyticsEvents(): HasMany
    {
        return $this->hasMany(AnalyticsEvent::class);
    }
}
