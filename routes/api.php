<?php

use App\Http\Controllers\MediaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CtaInteractionController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\AdminController;


Route::prefix('vehicle')
    ->middleware('verify.token')
    ->group(function () {

        // 30 requests/minute
        Route::post('/addVehicle', [VehicleController::class, 'addVehicle'])
            ->middleware('throttle:vehicle-write');

        // 30 requests/minute
        Route::put('/updateVehicle', [VehicleController::class, 'updateVehicle'])
            ->middleware('throttle:vehicle-write');

        // 30 requests/minute
        Route::put('/setVehicleStatus', [VehicleController::class, 'setVehicleStatus'])
            ->middleware('throttle:vehicle-write');

        // 30 requests/minute
        Route::delete('/deleteVehicle', [VehicleController::class, 'deleteVehicle'])
            ->middleware('throttle:vehicle-write');

        // 60 requests/minute
        Route::get('/filterVehicle', [VehicleController::class, 'filterVehicle'])
            ->middleware('throttle:vehicle-read');
    });


Route::prefix('admin')
    ->middleware('verify.token')
    ->group(function () {

        // 120 requests/minute
        Route::get('/getVehicleInventory', [VehicleController::class, 'getVehicleInventory'])
            ->middleware('throttle:admin-read');

        // 120 requests/minute
        Route::get('/getAdminDashboardOverview', [AdminController::class, 'getAdminDashboardOverview'])
            ->middleware('throttle:admin-read');

        // 120 requests/minute
        Route::get('/getInquiriesAdmin', [InquiryController::class, 'getInquiriesAdmin'])
            ->middleware('throttle:admin-read');

        // 30 requests/minute
        Route::post('/setInquiryState', [InquiryController::class, 'setInquiryState'])
            ->middleware('throttle:admin-write');
    });


Route::prefix('media')
    ->middleware('verify.token')
    ->group(function () {

        // 10 requests/minute
        Route::post('/vehicleFileUpload', [MediaController::class, 'vehicleFileUpload'])
            ->middleware('throttle:upload');
    });


Route::prefix('cta-interaction')
    ->middleware('verify.token')
    ->group(function () {

        // 30 requests/minute
        Route::post('/setInteraction', [CtaInteractionController::class, 'setInteraction'])
            ->middleware('throttle:cta');
    });


Route::prefix('inquiry')
    ->middleware('verify.token')
    ->group(function () {

        // 20 requests/minute
        Route::post('/addInquiry', [InquiryController::class, 'addInquiry'])
            ->middleware('throttle:inquiry');
    });


Route::prefix('adminAuth')->group(function () {

    // 30 requests/minute
    Route::post('/addStaff', [AdminAuthController::class, 'addStaff'])
        ->middleware('throttle:admin-write');

    // 5 requests/minute
    Route::post('/login', [AdminAuthController::class, 'login'])
        ->middleware('throttle:login');

    // 10 requests/minute
    Route::delete('/logout', [AdminAuthController::class, 'logout'])
        ->middleware('throttle:logout');
});



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
