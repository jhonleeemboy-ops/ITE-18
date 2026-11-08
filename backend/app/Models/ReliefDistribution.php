<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReliefDistribution extends Model
{
    protected $fillable = ['household_id', 'relief_item_id', 'quantity', 'distributed_at'];

    protected $casts = ['distributed_at' => 'datetime'];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function reliefItem(): BelongsTo
    {
        return $this->belongsTo(ReliefItem::class);
    }
}