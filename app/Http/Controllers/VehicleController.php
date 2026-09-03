<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Helpers\VehicleHelper;

class VehicleController extends Controller
{
    public function addVehicle(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|string|exists:admins,admin_id',
            'vin' => 'required|string',
            'year' => 'required|integer',
            'make' => 'required|string',
            'model' => 'required|string',
            'trim' => 'nullable|string',
            'condition' => 'nullable|string',
            'body_type' => 'nullable|string',
            'transmission' => 'nullable|string',
            'fuel_type' => 'nullable|string',
            'mileage' => 'nullable|integer',
            'engine' => 'nullable|string',
            'drivetrain' => 'nullable|string',
            'exterior_color' => 'nullable|string',
            'interior_color' => 'nullable|string',
            'doors' => 'nullable|integer',
            'seats' => 'nullable|integer',
            'location' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'init_state' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        return VehicleHelper::addVehicle($request);
    }


    public function setVehicleStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|string|exists:admins,admin_id',
            'veh_id' => 'required|string|exists:vehicles,veh_id',
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        return VehicleHelper::setVehicleStatus($request);
    }



    public function updateVehicle(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|string|exists:admins,admin_id',
            'veh_id' => 'required|string|exists:vehicles,veh_id',

            'vin' => 'nullable|string',
            'year' => 'nullable|integer',
            'make' => 'nullable|string',
            'model' => 'nullable|string',
            'trim' => 'nullable|string',
            'condition' => 'nullable|string',
            'body_type' => 'nullable|string',
            'transmission' => 'nullable|string',
            'fuel_type' => 'nullable|string',
            'mileage' => 'nullable|integer',
            'engine' => 'nullable|string',
            'drivetrain' => 'nullable|string',
            'exterior_color' => 'nullable|string',
            'interior_color' => 'nullable|string',
            'doors' => 'nullable|integer',
            'seats' => 'nullable|integer',
            'location' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        return VehicleHelper::updateVehicle($request);
    }


    public function filterVehicle(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'nullable|string',
            'page' => 'nullable|string',
            'vin' => 'nullable|string',
            'year' => 'nullable|integer',
            'make' => 'nullable|string',
            'model' => 'nullable|string',
            'trim' => 'nullable|string',
            'condition' => 'nullable|string',
            'body_type' => 'nullable|string',
            'transmission' => 'nullable|string',
            'fuel_type' => 'nullable|string',
            'mileage' => 'nullable|integer',
            'engine' => 'nullable|string',
            'drivetrain' => 'nullable|string',
            'exterior_color' => 'nullable|string',
            'interior_color' => 'nullable|string',
            'doors' => 'nullable|integer',
            'seats' => 'nullable|integer',
            'location' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        return VehicleHelper::filterVehicle($request);
    }



    public function deleteVehicle(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|string|exists:admins,admin_id',
            'veh_id' => 'required|string|exists:vehicles,veh_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        return VehicleHelper::deleteVehicle($request);
    }
}