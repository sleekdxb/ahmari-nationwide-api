<?php

namespace App\Helpers;

use App\Models\Vehicle;
use App\Models\VehicleStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Log;


class VehicleHelper
{

    public static function addVehicle(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();


            $vehId = 'VEHICLE' . str_pad(
                (string) random_int(0, 99999999),
                8,
                '0',
                STR_PAD_LEFT
            );

            $stateId = 'STATE' . str_pad(
                (string) random_int(0, 99999999),
                8,
                '0',
                STR_PAD_LEFT
            );

            $vehicle = Vehicle::create([
                'veh_id' => $vehId,
                'state_id' => $stateId,
                'admin_id' => $request->admin_id,
                'vin' => $request->vin,
                'stock_number' => $request->stock_number,
                'year' => $request->year,
                'make' => $request->make,
                'model' => $request->model,
                'trim' => $request->trim,
                'condition' => $request->condition,
                'body_type' => $request->body_type,
                'transmission' => $request->transmission,
                'fuel_type' => $request->fuel_type,
                'mileage' => $request->mileage,
                'engine' => $request->engine,
                'drivetrain' => $request->drivetrain,
                'exterior_color' => $request->exterior_color,
                'interior_color' => $request->interior_color,
                'doors' => $request->doors,
                'seats' => $request->seats,
                'location' => $request->location,
                'price' => $request->price,
                'description' => $request->description,
            ]);

            /*
             * Create initial Vehicle Status
             */
            $vehicleStatus = VehicleStatus::create([
                'state_id' => $stateId,
                'veh_id' => $vehId,
                'name' => $request->init_state,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vehicle added successfully',
                'data' => [
                    'vehicle' => $vehicle,
                    'status' => $vehicleStatus,
                ]
            ], 201);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to add vehicle',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}