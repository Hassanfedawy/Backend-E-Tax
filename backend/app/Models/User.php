<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
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
        'subscription_id',
        'is_admin',
        'is_approved',
        'national_id',  

    ];

    protected static function newFactory()
    {
        return \Database\Factories\UserFactory::new();
    }

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
    // Helper to get profile image
public function profileImage()
{
    return $this->attachments()->where('category', 'profile_image');
}

// Helper to get national ID attachment
public function nationalId()
{
    return $this->attachments()->where('category', 'national_id');
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
}
