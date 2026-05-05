<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('transactions', TransactionController::class)->except(['create', 'edit']);
    Route::resource('budgets', BudgetController::class)->except(['show', 'create', 'edit']);
    Route::post('/budgets/copy-previous', [BudgetController::class, 'copyPrevious'])->name('budgets.copy-previous');
});

require __DIR__.'/auth.php';
