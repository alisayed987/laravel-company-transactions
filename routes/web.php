<?php

use App\Livewire\CreateFinancials;
use App\Livewire\TransactionsAndPayments;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('/transactions');
})->middleware(['auth']);

Route::get('/createFinancials', CreateFinancials::class)
->middleware(['auth'])->name('/createFinancials');

Route::get('/transactions', TransactionsAndPayments::class)
->middleware(['auth'])->name('/transactions');

require __DIR__.'/auth.php';
