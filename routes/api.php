<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\BudgetApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\CryptoApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\ExchangeRateApiController;
use App\Http\Controllers\Api\ReportApiController;
use App\Http\Controllers\Api\TransactionApiController;
use Illuminate\Support\Facades\Route;

// Javne rute — bez autentifikacije
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login',    [AuthApiController::class, 'login']);

// Zaštićene rute — zahtijevaju Bearer token
Route::middleware('api.auth')->group(function () {

    // Autentifikacija
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/user',    [AuthApiController::class, 'user']);

    // Transakcije (apiResource sa prilagođenim imenima da nema konflikta sa web rutama)
    Route::apiResource('transactions', TransactionApiController::class)->names([
        'index'   => 'api.transactions.index',
        'store'   => 'api.transactions.store',
        'show'    => 'api.transactions.show',
        'update'  => 'api.transactions.update',
        'destroy' => 'api.transactions.destroy',
    ]);

    // Kategorije
    Route::get('/categories',         [CategoryApiController::class, 'index']);
    Route::post('/categories',        [CategoryApiController::class, 'store']);
    Route::put('/categories/{id}',    [CategoryApiController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryApiController::class, 'destroy']);

    // Budžeti
    Route::get('/budgets',  [BudgetApiController::class, 'index']);
    Route::post('/budgets', [BudgetApiController::class, 'store']);

    // Dashboard
    Route::get('/dashboard', [DashboardApiController::class, 'index']);

    // Izvještaji
    Route::get('/reports', [ReportApiController::class, 'index']);

    // Kursevi valuta (open.er-api.com)
    Route::get('/exchange-rates',         [ExchangeRateApiController::class, 'index']);
    Route::get('/exchange-rates/convert', [ExchangeRateApiController::class, 'convertBalance']);

    // Kripto kursevi (CoinGecko)
    Route::get('/crypto-prices', [CryptoApiController::class, 'index']);

    // Ugnježdene rute — transakcije i budžeti po kategoriji
    Route::get('/categories/{id}/transactions', [CategoryApiController::class, 'transactions']);
    Route::get('/categories/{id}/budgets',      [CategoryApiController::class, 'budgets']);
});
