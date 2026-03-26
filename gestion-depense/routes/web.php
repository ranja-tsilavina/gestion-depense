<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\BudgetController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/categories',[CategoryController::class,'index']);
    Route::post('/categories',[CategoryController::class,'store']);
    Route::delete('/categories/{id}',[CategoryController::class,'destroy']);
    
    Route::get('/expenses/export/excel', [ExpenseController::class, 'exportExcel'])->name('expenses.export.excel');
    Route::get('/expenses/export/pdf', [ExpenseController::class, 'exportPdf'])->name('expenses.export.pdf');
    Route::get('/expenses',[ExpenseController::class,'index'])->name('expenses.index');
    Route::get('/expenses/create',[ExpenseController::class,'create']);
    Route::post('/expenses',[ExpenseController::class,'store']);
    Route::delete('/expenses/{id}',[ExpenseController::class,'destroy']);

    Route::get('/revenues/export/excel', [RevenueController::class, 'exportExcel'])->name('revenues.export.excel');
    Route::get('/revenues/export/pdf', [RevenueController::class, 'exportPdf'])->name('revenues.export.pdf');
    Route::get('/revenues', [RevenueController::class, 'index'])->name('revenues.index');
    Route::get('/revenues/create', [RevenueController::class, 'create']);
    Route::post('/revenues', [RevenueController::class, 'store']);
    Route::delete('/revenues/{id}', [RevenueController::class, 'destroy']);

    Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
    Route::get('/budgets/create', [BudgetController::class, 'create']);
    Route::post('/budgets', [BudgetController::class, 'store']);
    Route::delete('/budgets/{id}', [BudgetController::class, 'destroy']);
});


require __DIR__.'/auth.php';
