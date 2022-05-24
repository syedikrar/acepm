<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'profile_picture', 'description', 'languages', 'country',
        'email', 'social_links', 'password', 'role', 'approved_at', 'email_verified_at',
        'login_platform', 'login_platform_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'languages' => 'array',
        'social_links' => 'array'
    ];

    /**
     * Generate random password
     *
     * @param int $length
     *
     * @return string
     */
    public static function generatePassword($length = 32)
    {
        return bcrypt(str_random($length));
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * @return int
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function shops()
    {
        return $this->belongsToMany(Shop::class);
    }

    public function skills()
    {
        return $this->hasMany(Skills::class);
    }

    public function gigs() {
        return $this->hasMany(Gig::class);
    }

    public function education()
    {
        return $this->hasOne(Education::class)->withDefault();
    }
    
    public function reviews(){
        return $this->hasMany(Reviews::class);
    }
    
    public function contracts() {
        return $this->hasMany(Contract::class);
    }

    public function boards(){
        return $this->belongsToMany(Board::class);
    }

    public function templates(){
        return $this->hasMany(BoardTemplate::class);
    }
}
