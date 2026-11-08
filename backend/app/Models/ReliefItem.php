<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReliefItem extends Model
{
    protected $fillable = ['name', 'unit', 'quantity_in_stock'];

    public function distributions(): HasMany
    {
        return $this->hasMany(ReliefDistribution::class);
    }
}