<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Household extends Model
{
    protected $fillable = ['head_name', 'purok', 'members', 'safety'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
        
    }

        // The people in the household. Not named "members" because
    // "members" is already the column that holds the head count.
    public function familyMembers(): HasMany
    {
        return $this->hasMany(HouseholdMember::class);
    }

}