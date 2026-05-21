<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    protected $fillable = ['session_token', 'entreprise_id', 'is_eligible', 'completed_at'];

    protected $casts = [
        'is_eligible' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class);
    }
}
