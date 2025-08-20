<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    public function post(){ return $this->belongsTo(Post::class); }
    public function user(){ return $this->belongsTo(User::class); }
    public function reactions(){ return $this->morphMany(Reaction::class, 'reactionable'); }
}
