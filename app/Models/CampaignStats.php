<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignStats extends Model
{
    protected $fillable = ['donations_count', 'lives_saved', 'hug_hospitals_count', 'updated_by'];

    protected $casts = [
        'donations_count' => 'integer',
        'lives_saved' => 'integer',
        'hug_hospitals_count' => 'integer',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Helper singleton pour respecter la contrainte métier [1]
    public static function getInstance(): self
    {
        return self::firstOrCreate(
            [], // Toujours vide car il n'y a qu'une ligne
            ['donations_count' => 0, 'lives_saved' => 0, 'hug_hospitals_count' => 0]
        );
    }
}
