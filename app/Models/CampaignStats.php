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

    public static function getInstance(): self
    {
        return self::firstOrCreate(
            [],
            ['donations_count' => 0, 'lives_saved' => 0, 'hug_hospitals_count' => 0]
        );
    }
}
