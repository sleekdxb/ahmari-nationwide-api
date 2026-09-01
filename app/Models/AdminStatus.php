<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminStatus extends Model
{
    protected $table = 'admins_status';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'admin_id',
        'state_id',
        'state',
        'note',
    ];
}
