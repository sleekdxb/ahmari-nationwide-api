<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleFileStatus extends Model
{
    use HasFactory;

    protected $table = 'vehicle_files_status';

    protected $fillable = [
        'file_id',
        'state_id',
        'name',
    ];
}
