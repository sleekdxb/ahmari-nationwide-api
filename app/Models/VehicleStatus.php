<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleStatus extends Model
{
    use HasFactory;

    protected $table = 'vehicle_status';

    protected $fillable = [
        'state_id',
        'veh_id',
        'name',
    ];
}
