<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable= [ 'attachable_type' ,
    'attachable_id',
    'category',
    'path',

];
    
    public function attachable(){ return $this->morphTo(); }
}
