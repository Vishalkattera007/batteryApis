<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\adminMaster;
use App\Http\Controllers\Api\DealerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::get('/admin', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('admins', [AdminController::class, 'index']);
Route::post('admins', [AdminController::class, 'create']);
Route::post('admins/{id}', [AdminController::class, 'update']);
Route::post('deleteadmin/{id}', [AdminController::class, 'delete']);





// Route for Dealer
Route::get('dealer', [DealerController::class, 'index']);
Route::post('dealer', [DealerController::class, 'create']);
Route::get('dealer/{id}', [DealerController::class, 'show']);
Route::put('dealer/{id}', [DealerController::class, 'update']);
Route::delete('dealer/{id}', [DealerController::class, 'destroy']);