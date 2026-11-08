<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentNote extends Model
{
    protected $fillable = ['incident_id', 'text'];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }
}