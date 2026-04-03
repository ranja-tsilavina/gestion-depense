<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Budget;
use App\Models\Expense;
use Carbon\Carbon;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('includes.header', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $householdId = session('active_household_id');
                
                $notifications = \App\Models\Notification::where(function($query) use ($user, $householdId) {
                    $query->where('user_id', $user->id);
                    if ($householdId) {
                        $query->orWhere('household_id', $householdId);
                    }
                })
                ->where('is_read', false)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($notif) {
                    return [
                        'id'      => $notif->id,
                        'type'    => $notif->type,
                        'title'   => $notif->type === 'danger' ? 'Alerte Critique' : ($notif->type === 'warning' ? 'Avertissement' : 'Information'),
                        'message' => $notif->message,
                        'percent' => 100 // fallback for progress bar UI
                    ];
                });

                $view->with('globalNotifications', $notifications);
            }
        });
    }
}
