<?php

use App\Modules\Api\Building\Controllers\BuildingController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'building', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [BuildingController::class, 'index'])->name('index');
    Route::post('/', [BuildingController::class, 'create'])->name('create');
    Route::put('/{building}', [BuildingController::class, 'update'])->name('update');
    Route::delete('/{building}', [BuildingController::class, 'destroy'])->name('destroy');
});
