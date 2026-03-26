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
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $userId = auth()->id();
                $now = Carbon::now();
                $month = $now->month;
                $year = $now->year;

                $categories = Category::all();
                $notifications = [];

                foreach ($categories as $category) {
                    $budget = Budget::where('user_id', $userId)
                        ->where('category_id', $category->id)
                        ->whereMonth('month', $month)
                        ->whereYear('month', $year)
                        ->first();

                    if ($budget && $budget->amount > 0) {
                        $expenses = Expense::where('user_id', $userId)
                            ->where('category_id', $category->id)
                            ->whereMonth('expense_date', $month)
                            ->whereYear('expense_date', $year)
                            ->sum('amount');

                        $percent = ($expenses / $budget->amount) * 100;

                        if ($percent >= 100) {
                            $notifications[] = [
                                'type' => 'danger',
                                'title' => 'Budget dépassé',
                                'message' => "Vous avez dépassé le budget de la catégorie '{$category->name}'.",
                                'category' => $category->name,
                                'percent' => round($percent)
                            ];
                        } elseif ($percent >= 80) {
                            $notifications[] = [
                                'type' => 'warning',
                                'title' => 'Alerte budget',
                                'message' => "Vous avez atteint " . round($percent) . "% du budget de la catégorie '{$category->name}'.",
                                'category' => $category->name,
                                'percent' => round($percent)
                            ];
                        }
                    }
                }
                $view->with('globalNotifications', $notifications);
            }
        });
    }
}
