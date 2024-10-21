<?php 

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\DealerController;


Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index']);     // List all admins
    Route::post('/', [AdminController::class, 'store']);    // Create new admin
    Route::get('/{id}', [AdminController::class, 'show']);  // Show specific admin by ID
    Route::put('/{id}', [AdminController::class, 'update']); // Update specific admin
    Route::delete('/{id}', [AdminController::class, 'destroy']); // Delete specific admin
});

Route::prefix('dealer')->group(function () {
    Route::get('/', [DealerController::class, 'index']);     // List all dealers
    Route::post('/', [DealerController::class, 'store']);    // Create a new dealer
    Route::get('/{id}', [DealerController::class, 'show']);  // Show a specific dealer by ID
    Route::put('/{id}', [DealerController::class, 'update']); // Update a specific dealer
    Route::delete('/{id}', [DealerController::class, 'destroy']); // Delete a specific dealer
});

