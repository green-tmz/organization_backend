<?php

use App\Modules\Api\Building\Controllers\BuildingController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'building', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [BuildingController::class, 'index'])->name('building.index');
    Route::get('/{building}', [BuildingController::class, 'show'])->name('building.show');
    Route::post('/', [BuildingController::class, 'create'])->name('building.create');
    Route::put('/{building}', [BuildingController::class, 'update'])->name('building.update');
    Route::delete('/{building}', [BuildingController::class, 'destroy'])->name('building.destroy');
});
