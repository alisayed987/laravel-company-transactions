<?php

use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\TransactionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/createTransaction', [TransactionsController::class, 'createTransaction'])
    ->middleware(['auth:sanctum']);

Route::post('/allTransactions', [TransactionsController::class, 'allTransactions'])
    ->middleware(['auth:sanctum']);

Route::post('/customerTransactions', [TransactionsController::class, 'customerTransactions'])
    ->middleware(['auth:sanctum']);

Route::post('/getReport', [TransactionsController::class, 'getReport'])
    ->middleware(['auth:sanctum']);

Route::post('/addPayment', [PaymentsController::class, 'addPayment'])
    ->middleware(['auth:sanctum']);

Route::post('/getTransactionPayments', [PaymentsController::class, 'getTransactionPayments'])
    ->middleware(['auth:sanctum']);


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
