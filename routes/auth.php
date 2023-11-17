<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Route;


Route::get('/login', Login::class)
                ->middleware('guest')
                ->name('login');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->middleware('auth')
                ->name('logout');
