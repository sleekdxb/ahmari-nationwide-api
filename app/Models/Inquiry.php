<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $table = 'inquiries';

    protected $fillable = [
        'client_id',
        'inq_id',
        'name',
        'phone_number',
        'email',
        'message',
    ];

    protected $casts = [
        'client_id' => 'string',
        'inq_id' => 'string',
        'name' => 'string',
        'phone_number' => 'string',
        'email' => 'string',
        'message' => 'string',
    ];
}

