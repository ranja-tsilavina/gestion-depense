<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Category;
use App\Models\Budget;

class ExpenseController extends Controller
{

    public function index(Request $request)
    {
        $userId = auth()->id();
        
        $selectedYear = $request->input('year', date('Y'));
        $selectedMonth = $request->input('month', date('m'));
        
        if ($request->has('year') && !$request->filled('month')) {
            $selectedMonth = null;
        }

        // Base Query for Expenses
        $query = Expense::with('category')->where('user_id', $userId);
        
        if ($selectedYear) {
            $query->whereYear('expense_date', $selectedYear);
        }
        if ($selectedMonth) {
            $query->whereMonth('expense_date', $selectedMonth);
        }

        $expenses = (clone $query)->latest()->get();

        // ── Monthly totals (last 12 months from the selected date context or current) ──
        // Only makes sense if "All months" is selected, otherwise it's just one month. Let's keep the logic but filter by year strictly if year is selected.
        $monthlyQuery = Expense::where('user_id', $userId);
        if ($selectedYear) {
            $monthlyQuery->whereYear('expense_date', $selectedYear);
        }
        if ($selectedMonth) {
            $monthlyQuery->whereMonth('expense_date', $selectedMonth);
        }
        
        $monthlyRaw = $monthlyQuery
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->pluck('total', 'month');

        $monthlyLabels = $monthlyRaw->keys()->map(function ($m) {
            return \Carbon\Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y');
        })->values();
        $monthlyTotals = $monthlyRaw->values();

        // ── Per-category totals ──
        $categoryRaw = (clone $query)
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get()
            ->map(fn($e) => [
                'name'  => $e->category->name ?? 'Inconnu',
                'total' => (int) $e->total,
            ]);

        $categoryLabels = $categoryRaw->pluck('name');
        $categoryTotals = $categoryRaw->pluck('total');

        // ── Budget alerts ──
        $categories = Category::all();
        $alertes = [];

        foreach ($categories as $category) {
            $catExpQuery = Expense::where('user_id', $userId)
                                ->where('category_id', $category->id);
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

            $totalDepenses = $catExpQuery->sum('amount');
            
            if ($selectedMonth) {
                $budget = $catBudgetQuery->first();
                $budgetAmount = $budget ? $budget->amount : 0;
            } else {
                $budgetAmount = $catBudgetQuery->sum('amount');
            }

            if ($budgetAmount > 0 && $totalDepenses > $budgetAmount) {
                $period = $selectedMonth ? "du mois" : "de l'année";
                $alertes[] = "Attention : Vous avez dépassé le budget {$period} de la catégorie '{$category->name}' !";
            }
        }

        return view('expenses.index', compact(
            'expenses',
            'monthlyLabels', 'monthlyTotals',
            'categoryLabels', 'categoryTotals',
            'alertes',
            'selectedYear',
            'selectedMonth'
        ));
    }


    public function create()
    {
        $categories = Category::all();
        return view('expenses.create',compact('categories'));
    }


    public function store(Request $request)
    {
        Expense::create([
            'user_id'=>auth()->id(),
            'category_id'=>$request->category_id,
            'amount'=>$request->amount,
            'description'=>$request->description,
            'expense_date'=>$request->expense_date
        ]);

        return redirect()->route('expenses.index');
    }


    public function destroy($id)
    {
        Expense::findOrFail($id)->delete();
        return redirect()->back();
    }
}
