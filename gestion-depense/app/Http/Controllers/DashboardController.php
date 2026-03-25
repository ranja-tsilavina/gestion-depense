<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Revenue;
use App\Models\Budget;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // 1. Total Stats (All time)
        $totalExpenses = Expense::where('user_id', $userId)->sum('amount');
        $totalRevenues = Revenue::where('user_id', $userId)->sum('amount');

        // 2. Budget Alerts (Current month)
        $categories = Category::all();
        $alertes = [];

        foreach ($categories as $category) {
            $catExpenses = Expense::where('user_id', $userId)
                ->where('category_id', $category->id)
                ->whereMonth('expense_date', $currentMonth)
                ->whereYear('expense_date', $currentYear)
                ->sum('amount');

            $budget = Budget::where('user_id', $userId)
                ->where('category_id', $category->id)
                ->whereMonth('month', $currentMonth)
                ->whereYear('month', $currentYear)
                ->first();

            if ($budget && $catExpenses > $budget->amount) {
                $alertes[] = "Attention : Vous avez dépassé le budget mensuel de la catégorie '{$category->name}' !";
            }
        }

        // 3. Charts Data (Current month expenses vs budget)
        $chartCategories = [];
        $chartExpenses = [];
        $chartBudgets = [];

        foreach ($categories as $category) {
            $catExpenses = Expense::where('user_id', $userId)
                ->where('category_id', $category->id)
                ->whereMonth('expense_date', $currentMonth)
                ->whereYear('expense_date', $currentYear)
                ->sum('amount');

            $budget = Budget::where('user_id', $userId)
                ->where('category_id', $category->id)
                ->whereMonth('month', $currentMonth)
                ->whereYear('month', $currentYear)
                ->first();

            $budgetAmount = $budget ? $budget->amount : 0;

            // Only include categories that have either an expense or a budget this month
            if ($catExpenses > 0 || $budgetAmount > 0) {
                $chartCategories[] = $category->name;
                $chartExpenses[] = $catExpenses;
                $chartBudgets[] = $budgetAmount;
            }
        }

        return view('dashboard', compact(
            'totalExpenses',
            'totalRevenues',
            'alertes',
            'chartCategories',
            'chartExpenses',
            'chartBudgets'
        ));
    }
}
