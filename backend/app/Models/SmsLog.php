<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    protected $fillable = ['advisory_id', 'recipients', 'sent', 'failed', 'sent_at'];

    protected $casts = ['sent_at' => 'datetime'];

    public function advisory(): BelongsTo
    {
        return $this->belongsTo(Advisory::class);
    }
}