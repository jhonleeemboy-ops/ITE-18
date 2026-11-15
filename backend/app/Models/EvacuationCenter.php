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

    // The status follows the number of people inside:
    // empty = standby, 90% full or more = near_capacity, otherwise open
    public static function statusFor(int $occupants, int $capacity): string
    {
        if ($occupants === 0) {
            return 'standby';
        }

        return $occupants >= $capacity * 0.9 ? 'near_capacity' : 'open';
    }
}