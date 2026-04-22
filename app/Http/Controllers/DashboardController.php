<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Revenue;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Filters
        $selectedYear  = $request->input('year', Carbon::now()->year);
        $selectedMonth = $request->input('month', Carbon::now()->month);
        if ($request->has('year') && !$request->filled('month')) {
            $selectedMonth = null;
        }

        // 1. Total Stats – household scope applied automatically by BelongsToHousehold
        $expensesQuery = Expense::query();
        $revenuesQuery = Revenue::query();

        if ($selectedYear) {
            $expensesQuery->whereYear('expense_date', $selectedYear);
            $revenuesQuery->whereYear('revenue_date', $selectedYear);
        }
        if ($selectedMonth) {
            $expensesQuery->whereMonth('expense_date', $selectedMonth);
            $revenuesQuery->whereMonth('revenue_date', $selectedMonth);
        }

        $totalExpenses = $expensesQuery->sum('amount');
        $totalRevenues = $revenuesQuery->sum('amount');

        // 2. Account Balances (Current)
        $accounts = Account::all();
        $totalBalance = $accounts->sum('balance');

        // 3. Savings Rate
        $savingsRate = 0;
        if ($totalRevenues > 0) {
            $savingsRate = (($totalRevenues - $totalExpenses) / $totalRevenues) * 100;
        }

        // 4. Financial Forecast
        $forecast = 0;
        $showForecast = false;
        $forecastWarning = false;
        
        if ($selectedMonth && $selectedYear) {
            $isCurrentMonth = ($selectedMonth == Carbon::now()->month && $selectedYear == Carbon::now()->year);
            if ($isCurrentMonth) {
                $daysInMonth = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->daysInMonth;
                $daysPassed = Carbon::now()->day;
                $showForecast = true;
                
                if ($daysPassed > 0) {
                    $averageDaily = $totalExpenses / $daysPassed;
                    $forecast = $averageDaily * $daysInMonth;
                }
            }
        }
        
        $totalBudget = Budget::query();
        if ($selectedYear) $totalBudget->whereYear('month', $selectedYear);
        if ($selectedMonth) $totalBudget->whereMonth('month', $selectedMonth);
        $totalBudgetAmount = $totalBudget->sum('amount');
        
        if ($showForecast && $totalBudgetAmount > 0) {
            $forecastWarning = ($forecast > $totalBudgetAmount);
        }

        // 2. Budget Alerts & 3. Charts Data
        $categories = Category::all();
        $alertes = [];
        $chartCategories = [];
        $chartExpenses = [];
        $chartBudgets = [];

        // Optimized Queries (No N+1)
        $expenseStatsQuery = Expense::selectRaw('category_id, SUM(amount) as total');
        if ($selectedYear) $expenseStatsQuery->whereYear('expense_date', $selectedYear);
        if ($selectedMonth) $expenseStatsQuery->whereMonth('expense_date', $selectedMonth);
        $expenseStats = $expenseStatsQuery->groupBy('category_id')->pluck('total', 'category_id');

        $budgetStatsQuery = Budget::selectRaw('category_id, SUM(amount) as total');
        if ($selectedYear) $budgetStatsQuery->whereYear('month', $selectedYear);
        if ($selectedMonth) $budgetStatsQuery->whereMonth('month', $selectedMonth);
        $budgetStats = $budgetStatsQuery->groupBy('category_id')->pluck('total', 'category_id');

        foreach ($categories as $category) {
            $catExpenses = $expenseStats[$category->id] ?? 0;
            $budgetAmount = $budgetStats[$category->id] ?? 0;

            // Alerts
            if ($budgetAmount > 0) {
                $percentage = ($catExpenses / $budgetAmount) * 100;
                $period = $selectedMonth ? "du mois" : "de l'année";

                if ($percentage >= 100) {
                    $alertes[] = [
                        'type' => 'danger',
                        'message' => "Attention : Vous avez dépassé le budget {$period} de la catégorie '{$category->name}' !"
                    ];
                } elseif ($percentage >= 80) {
                    $alertes[] = [
                        'type' => 'warning',
                        'message' => "Alerte : Vous avez atteint " . round($percentage) . "% du budget {$period} de la catégorie '{$category->name}'."
                    ];
                }
            }

            // Charts
            if ($catExpenses > 0 || $budgetAmount > 0) {
                $chartCategories[] = $category->name;
                $chartExpenses[] = $catExpenses;
                $chartBudgets[] = $budgetAmount;
            }
        }

        return view('dashboard', compact(
            'totalExpenses',
            'totalRevenues',
            'totalBalance',
            'savingsRate',
            'accounts',
            'alertes',
            'chartCategories',
            'chartExpenses',
            'chartBudgets',
            'selectedYear',
            'selectedMonth',
            'forecast',
            'showForecast',
            'forecastWarning'
        ));
    }
}
