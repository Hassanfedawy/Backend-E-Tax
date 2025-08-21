<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
     public function users() {
        return $this->hasMany(User::class);
    }
    use HasFactory;

    protected $fillable = [
        'name',
        'cost',
        'no_of_posts',
    ];
}
