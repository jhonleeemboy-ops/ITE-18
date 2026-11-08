<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvacuationCenter extends Model
{
    protected $fillable = ['name', 'capacity', 'occupants', 'status'];

    public function puroks(): HasMany
    {
        return $this->hasMany(CenterPurok::class);
    }
}