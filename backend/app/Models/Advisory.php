<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advisory extends Model
{
   protected $fillable = ['title','message','severity','status','issued_at'];
protected $casts = ['issued_at' => 'datetime'];
}
