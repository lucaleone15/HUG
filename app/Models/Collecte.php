<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Collecte extends Model
{
    protected $fillable = [
        'entreprise_id',
        'ondoc_url',
        'rdv_date',
        'label',
        'is_active',
    ];

    protected $casts = [
        'rdv_date'  => 'date:Y-m-d',
        'is_active' => 'boolean',
    ];

    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class);
    }
}
