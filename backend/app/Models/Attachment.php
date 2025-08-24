<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    
    protected $fillable = ['category', 'path', 'attachable_id', 'attachable_type'];
    public function attachable(){ return $this->morphTo(); }
}
