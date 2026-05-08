<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExpenseSyncController;

/*
|--------------------------------------------------------------------------
| API Routes – PWA Sync
|--------------------------------------------------------------------------
| These routes use the 'web' middleware group so they share the same
| session & CSRF protection as web routes. No Sanctum token needed —
| the PWA is served from the same origin and uses session cookies.
|
| The Service Worker sends the CSRF token stored in localStorage by
| pwa-manager.js (fetched from /api/expenses/form-data on first load).
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth'])->group(function () {

    // Returns categories + accounts + CSRF token for the offline form
    Route::get('/expenses/form-data', [ExpenseSyncController::class, 'formData'])
        ->name('api.expenses.form-data');

    // Receives queued offline expenses from the Service Worker
    Route::post('/expenses/sync', [ExpenseSyncController::class, 'sync'])
        ->name('api.expenses.sync');
});
