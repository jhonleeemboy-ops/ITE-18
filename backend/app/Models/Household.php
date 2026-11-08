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
}