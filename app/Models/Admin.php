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
     * JWT subject identifier.
     *
     * The admins table uses "id" as the primary key,
     * so JWTAuth will store that ID in the "sub" claim.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Custom JWT claims.
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
