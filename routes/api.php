<?php

use App\Http\Controllers\MediaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\AdminAuthController;

Route::prefix('vehicle')->group(function () {
    Route::post('/addVehicle', [VehicleController::class, 'addVehicle']);
    Route::put('/updateVehicle', [VehicleController::class, 'updateVehicle']);
    Route::put('/setVehicleStatus', [VehicleController::class, 'setVehicleStatus']);
    Route::delete('/deleteVehicle', [VehicleController::class, 'deleteVehicle']);
    Route::get('/filterVehicle', [VehicleController::class, 'filterVehicle']);

});


Route::prefix('admin')->group(function () {
    Route::get('/getVehicleInventory', [VehicleController::class, 'getVehicleInventory']);
});

Route::prefix('media')->group(function () {
    Route::post('/vehicleFileUpload', [MediaController::class, 'vehicleFileUpload']);
});


Route::prefix('adminAuth')->group(function () {
    Route::post('/addStaff', [AdminAuthController::class, 'addStaff']);
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::delete('/logout', [AdminAuthController::class, 'logout']);
});



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
