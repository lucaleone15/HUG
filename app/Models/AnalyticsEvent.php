<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsEvent extends Model
{
    // Types d'événements possibles
    const TYPES = [
        'page_viewed',
        'quiz_started',
        'question_answered',
        'quiz_abandoned',
        'quiz_completed',
        'rdv_clicked',
        'kit_downloaded'
    ];

    protected $fillable = ['type', 'entreprise_id', 'session_token', 'metadata'];

    protected $casts = [
        'type' => self::TYPES, // Utilise un enum PHP 8.1+ en prod
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class);
    }
}
