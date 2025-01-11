<?php

use App\Modules\Api\Auth\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => ''], function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum')
        ->name('logout');
});
