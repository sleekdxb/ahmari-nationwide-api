<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'veh_id',
        'admin_id',
        'stock_number',
        'vin',
        'year',
        'make',
        'model',
        'trim',
        'condition',
        'body_type',
        'transmission',
        'fuel_type',
        'mileage',
        'engine',
        'drivetrain',
        'exterior_color',
        'interior_color',
        'doors',
        'seats',
        'state_id',
        'location',
        'price',
        'description',
    ];

    protected $casts = [
        'year' => 'integer',
        'mileage' => 'integer',
        'doors' => 'integer',
        'seats' => 'integer',
        'price' => 'decimal:2',
    ];


    public function files()
    {
        return $this->hasMany(
            VehicleFile::class,
            'veh_id',
            'veh_id'
        );
    }



    public function currentState()
    {
        return $this->hasOne(
            VehicleStatus::class,
            'state_id',
            'state_id'
        );
    }
}
