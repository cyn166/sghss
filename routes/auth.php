<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::post('/api/register', [RegisteredUserController::class, 'store'])
    ->name('register');

Route::post('/api/login', [AuthenticatedSessionController::class, 'store'])
    ->name('login');

Route::delete('/api/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum')
    ->name('logout');
