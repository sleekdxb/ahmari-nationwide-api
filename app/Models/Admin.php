<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'admins';

    protected $fillable = [
        'admin_id',
        'first_name',
        'last_name',
        'email',
        'state_id',
        'hashed_email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the identifier that will be stored in the JWT subject claim.
     */
    public function getJWTIdentifier()
    {
        return $this->admin_id;
    }

    /**
     * Get custom claims for the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function currentState()
    {
        return $this->hasOne(
            AdminStatus::class,
            'state_id',
            'state_id'
        );
    }
}

