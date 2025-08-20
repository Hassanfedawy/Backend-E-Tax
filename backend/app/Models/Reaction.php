<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    
    public function reactionable(){ return $this->morphTo(); }
    public function user(){ return $this->belongsTo(User::class); }
}
