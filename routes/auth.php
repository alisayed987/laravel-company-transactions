<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Route;

/**
 * Livewire full-page component API
 */
Route::get('/login', Login::class)
                ->middleware('guest')
                ->name('login');

/**
 * General API
 */
Route::post('/register', [RegisteredUserController::class, 'store'])
                ->middleware('guest')
                ->name('register');

/**
 * General API
 */
Route::post('login', [AuthenticatedSessionController::class, 'store'])
                ->middleware('guest')
                ->name('login');

/**
 * General API
 */
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->middleware('auth')
                ->name('logout');
