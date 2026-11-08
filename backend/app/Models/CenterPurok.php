<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CenterPurok extends Model
{
    protected $table = 'center_purok';

    protected $fillable = ['evacuation_center_id', 'purok'];

    public $timestamps = false;

    public function evacuationCenter(): BelongsTo
    {
        return $this->belongsTo(EvacuationCenter::class);
    }
}