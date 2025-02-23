<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'mobile_number',
        'status',
        'email',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_uid',
        'role',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'profile_filled' => 'boolean',
    ];
    public function getProfilePictureAttribute($value)
    {
        return $value != null ? config('app.media_base_url') . $value : $value;
    }
    public function otps()
    {
        return $this->hasMany(Otp::class);
    }
    
    public function preferredLanguage()
    {
        return $this->belongsTo(Language::class, 'preferred_language_id');
    }

    public function preferredAiRole()
    {
        return $this->belongsTo(AiRole::class, 'preferred_ai_role_id');
    }

}
