<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    public function attachable(){ return $this->morphTo(); }
}
