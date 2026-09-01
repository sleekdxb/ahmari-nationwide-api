<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminPasswordResetToken extends Model
{
    protected $table = 'admin_password_reset_tokens';

    public $timestamps = false;

    protected $primaryKey = 'email';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }
}
