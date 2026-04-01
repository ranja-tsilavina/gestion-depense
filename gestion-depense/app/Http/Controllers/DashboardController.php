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
        $userId = Auth::id();
        
        // Default to current month and year if no filters are applied, unless "all" or specific year is requested
        // Actually, if they want "Year only", month will be empty.
        // Let's set default: if no year is provided, default to current year.
        // If no month is provided and no year is provided, default to current month.
        $selectedYear = $request->input('year', Carbon::now()->year);
        $selectedMonth = $request->input('month', Carbon::now()->month);
        
        // If user explicitly submitted the form and chose "Tous les mois" (empty value), $selectedMonth will be null.
        if ($request->has('year') && !$request->filled('month')) {
            $selectedMonth = null;
        }

        // 1. Total Stats (Filtered)
        $expensesQuery = Expense::where('user_id', $userId);
        $revenuesQuery = Revenue::where('user_id', $userId);

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
        
        $totalBudget = Budget::where('user_id', $userId);
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

        foreach ($categories as $category) {
            // Expenses for this category
            $catExpQuery = Expense::where('user_id', $userId)
                ->where('category_id', $category->id);
            
            // Budget for this category
            $catBudgetQuery = Budget::where('user_id', $userId)
                ->where('category_id', $category->id);

            if ($selectedYear) {
                $catExpQuery->whereYear('expense_date', $selectedYear);
                $catBudgetQuery->whereYear('month', $selectedYear);
            }
            if ($selectedMonth) {
                $catExpQuery->whereMonth('expense_date', $selectedMonth);
                $catBudgetQuery->whereMonth('month', $selectedMonth);
            }

            $catExpenses = $catExpQuery->sum('amount');
            
            // If filtering by specific month, get that month's budget.
            // If filtering by year, sum the budgets for all months in that year.
            if ($selectedMonth) {
                $budget = $catBudgetQuery->first();
                $budgetAmount = $budget ? $budget->amount : 0;
            } else {
                $budgetAmount = $catBudgetQuery->sum('amount');
            }

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
