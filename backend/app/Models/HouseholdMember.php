<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HouseholdMember extends Model
{
    protected $fillable = ['household_id', 'name', 'age', 'relationship', 'is_pwd'];

    protected $casts = ['is_pwd' => 'boolean'];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }
}