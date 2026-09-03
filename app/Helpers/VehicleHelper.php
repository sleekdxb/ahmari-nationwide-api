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


    public static function setVehicleStatus(Request $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {

            $stateId = 'STATE' . str_pad(
                (string) random_int(0, 99999999),
                8,
                '0',
                STR_PAD_LEFT
            );

            // Create vehicle status
            $vehicleStatus = VehicleStatus::create([
                'state_id' => $stateId,
                'veh_id' => $request->veh_id,
                'name' => $request->status,
            ]);

            // Update vehicle's state_id
            $vehicle = Vehicle::where('veh_id', $request->veh_id)->first();

            if (!$vehicle) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle not found.'
                ], 404);
            }

            $vehicle->state_id = $stateId;
            $vehicle->save();

            return response()->json([
                'success' => true,
                'message' => 'Vehicle status updated successfully.',
                'data' => [
                    'state_id' => $stateId,
                    'veh_id' => $vehicle->veh_id,
                    'status' => $vehicleStatus->name,
                ]
            ], 200);
        });
    }



    public static function updateVehicle(Request $request)
    {
        $vehicle = Vehicle::where('veh_id', $request->veh_id)->first();

        if (!$vehicle) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle not found'
            ], 404);
        }

        $fields = [
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
            'location',
            'price',
            'description',
        ];

        $updateData = [];

        foreach ($fields as $field) {
            if ($request->has($field) && $request->input($field) !== null) {
                $updateData[$field] = $request->input($field);
            }
        }

        if (!empty($updateData)) {
            $vehicle->update($updateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Vehicle updated successfully',
            'data' => $vehicle->fresh()
        ]);
    }
}