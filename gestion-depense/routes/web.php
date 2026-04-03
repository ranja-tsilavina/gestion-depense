<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\HouseholdController;

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

    // Routes accessible by all household members (Owner & Member)
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/expenses/export/excel', [ExpenseController::class, 'exportExcel'])->name('expenses.export.excel');
    Route::get('/expenses/export/pdf', [ExpenseController::class, 'exportPdf'])->name('expenses.export.pdf');
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/create', [ExpenseController::class, 'create']);
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);
    
    // Budgets are read-only for members
    Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');

    Route::get('/households', [HouseholdController::class, 'index'])->name('households.index');
    Route::post('/households', [HouseholdController::class, 'store'])->name('households.store');
    Route::get('/households/switch/{id}', [HouseholdController::class, 'switch'])->name('households.switch');

    Route::post('/notifications/{id}/read', function ($id) {
        \App\Models\Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->update(['is_read' => true]);
        return back();
    })->name('notifications.read');

    // Owner only routes
    Route::middleware('role:owner')->group(function () {
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

        Route::get('/revenues/export/excel', [RevenueController::class, 'exportExcel'])->name('revenues.export.excel');
        Route::get('/revenues/export/pdf', [RevenueController::class, 'exportPdf'])->name('revenues.export.pdf');
        Route::get('/revenues', [RevenueController::class, 'index'])->name('revenues.index');
        Route::get('/revenues/create', [RevenueController::class, 'create']);
        Route::post('/revenues', [RevenueController::class, 'store']);
        Route::delete('/revenues/{id}', [RevenueController::class, 'destroy']);

        Route::get('/budgets/create', [BudgetController::class, 'create']);
        Route::post('/budgets', [BudgetController::class, 'store']);
        Route::delete('/budgets/{id}', [BudgetController::class, 'destroy']);

        Route::resource('accounts', AccountController::class);
        Route::resource('transfers', TransferController::class)->only(['index', 'create', 'store']);
        
        Route::get('/activities', [\App\Http\Controllers\ActivityController::class, 'index'])->name('activities.index');

        Route::get('/households/members', [HouseholdController::class, 'members'])->name('households.members');
        Route::post('/households/members', [HouseholdController::class, 'addMember'])->name('households.add_member');
        Route::delete('/households/members/{user}', [HouseholdController::class, 'removeMember'])->name('households.remove_member');
    });
});


require __DIR__.'/auth.php';
