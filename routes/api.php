<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;


Route::prefix('vehicle')->group(function () {
    Route::post('/addVehicle', [VehicleController::class, 'addVehicle']);
    Route::put('/updateVehicle', [VehicleController::class, 'updateVehicle']);
    Route::put('/setVehicle', [VehicleController::class, 'setVehicle']);
    Route::delete('/deleteVehicle', [VehicleController::class, 'deleteVehicle']);
    Route::get('/filterVehicle', [VehicleController::class, 'filterVehicle']);
    Route::get('/getVehicleInventory', [VehicleController::class, 'getVehicleInventory']);
});


Route::prefix('media')->group(function () {
    Route::post('/fileUpload', [VehicleController::class, 'fileUpload']);
});

Route::prefix('adminAuth')->group(function () {
    Route::post('/login', [VehicleController::class, 'login']);
    Route::post('/addStaff', [VehicleController::class, 'addStaff']);
});



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
