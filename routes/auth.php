<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\TokensController;
use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Route;


Route::get('/login', Login::class)
                ->middleware('guest')
                ->name('login');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->middleware('auth')
                ->name('logout');

/**
 * Mobile Auth APIs
 */
Route::post('/mobile/register', [TokensController::class, 'register']);
Route::post('/mobile/getToken', [TokensController::class, 'getLoginToken']);
Route::post('/mobile/revokeToken', [TokensController::class, 'revokeToken']);
Route::post('/mobile/revokeAllTokens', [TokensController::class, 'revokeAllTokens']);
