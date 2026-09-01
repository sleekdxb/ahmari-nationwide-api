<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleFile extends Model
{
    use HasFactory;

    protected $table = 'vehicle_files';

    protected $fillable = [
        'file_id',
        'veh_id',
        'file_name',
        'file_path',
        'file_url',
        'file_type',
        'state_id',
    ];
}
