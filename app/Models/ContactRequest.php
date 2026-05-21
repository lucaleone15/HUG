<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactRequest extends Model
{
    const TYPES = ['contact', 'collecte_inscription', 'trophee_candidature'];
    const STATUSES = ['pending', 'processed', 'rejected'];

    protected $fillable = ['type', 'name', 'email', 'company_name', 'subject', 'message', 'status', 'processed_by', 'processed_at'];

    protected $casts = [
        'type' => self::TYPES,
        'status' => self::STATUSES,
        'processed_at' => 'datetime',
    ];

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
