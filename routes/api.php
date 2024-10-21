<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\adminMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::get('/admin', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('admins', [adminMaster::class, 'index']);
Route::post('admins', [adminMaster::class, 'create']);
