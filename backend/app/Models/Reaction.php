<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    protected $fillable = [
    'user_id',
    'reactionable_type',
    'reactionable_id',
    'type',
];

    
    public function reactionable(){ return $this->morphTo(); }
    public function user(){ return $this->belongsTo(User::class); }
}
