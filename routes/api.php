<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AssignBatteryController;
use App\Http\Controllers\Api\BatteryRegController;
use App\Http\Controllers\Api\DealerController;
use App\Http\Controllers\Api\categoryController;
use App\Http\Controllers\Api\subCategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\BatteryMastController;

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

//customer login
Route::get('customerFind',[BatteryRegController::class, 'findCustomer']);



// Category Route
Route::get('category/{id?}', [CategoryController::class, 'index']);
Route::post('category', [CategoryController::class, 'create']);
Route::put('category/{id}', [CategoryController::class, 'update']);
Route::delete('deletecategory/{id}', [CategoryController::class, 'delete']);

//filter - category by Category Id
Route::get('categoryfilter/{id?}',[categoryController::class, 'filterCate']);

// subCategory Route

Route::get('subcategory/{id?}', [subCategoryController::class, 'index']);
Route::post('subcategory', [subCategoryController::class, 'create']);
Route::put('subcategory/{id}', [subCategoryController::class, 'update']);
Route::delete('deletesubcategory/{id}', [subCategoryController::class, 'delete']);


//Battery_master
Route::get('battery/{id?}', [BatteryMastController::class, 'index']);
Route::post('battery', [BatteryMastController::class, 'create']);
Route::put('battery/{id}', [BatteryMastController::class, 'update']);
Route::delete('deletebattery/{id}', [BatteryMastController::class, 'delete']);

//Battery Assign
Route::get('batteryAssign/{id?}', [AssignBatteryController::class, 'index']);
Route::post('batteryAssign', [AssignBatteryController::class, 'create']);
Route::put('batteryAssign/{id}', [AssignBatteryController::class, 'update']);
Route::delete('deleteAssign/{id}', [AssignBatteryController::class, 'delete']);