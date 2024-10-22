<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\BatteryRegController;
use App\Http\Controllers\Api\DealerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


// Route::get('/admin', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route for Admin
Route::get('admin/{id?}', [AdminController::class, 'index']);
Route::post('admin', [AdminController::class, 'create']);
Route::put('admin/{id}', [AdminController::class, 'update']);
Route::delete('deleteadmin/{id}', [AdminController::class, 'delete']);
Route::post('/admin/login', [AdminController::class, 'login']);


// Route for Dealer
Route::get('dealer', [DealerController::class, 'index']);
Route::post('dealer', [DealerController::class, 'create']);
Route::get('dealer/{id}', [DealerController::class, 'show']);
Route::put('dealer/{id}', [DealerController::class, 'update']);
Route::delete('dealer/{id}', [DealerController::class, 'destroy']);
Route::post('/dealer/login', [DealerController::class, 'login']);


//Battery Reg
Route::get('batteryReg/{id?}',[BatteryRegController::class, 'index']);
Route::post('batteryReg', [BatteryRegController::class, 'create']);
Route::put('batteryReg/{id}', [BatteryRegController::class, 'update']);
Route::delete('deletebatteryReg/{id}', [BatteryRegController::class, 'delete']);