<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Tymon\JWTAuth\Contracts\JWTSubject; 
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'national_id',
        'profile_picture',
        'national_id_image',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
   
    public function posts(){ return $this->hasMany(Post::class); }
    public function attachments(){ return $this->morphMany(Attachment::class, 'attachable'); }
    public function subscription() {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


/*
    public function sendPasswordResetNotification($token){
        $this->notify(new ResetPasswordNotification($token));
    }
    */

     // ================= JWT Required Methods =================

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));   
    }

    public function sendEmailVerificationNotification()
    {
    $this->notify(new CustomVerifyEmail);
    }


}
