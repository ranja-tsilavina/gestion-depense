<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Category;
use App\Models\Budget;
use App\Exports\ExpensesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    private function getFilteredQuery(Request $request)
    {
        $query = Expense::with(['category', 'creator']);
        
        $selectedYear = $request->input('year');
        $selectedMonth = $request->input('month');
        if ($request->has('year') && !$request->filled('month')) {
            $selectedMonth = null;
        }

        if ($selectedYear && $selectedYear !== '') {
            $query->whereYear('expense_date', $selectedYear);
        }
        if ($selectedMonth) {
            $query->whereMonth('expense_date', $selectedMonth);
        }
        
        $categoryId = $request->input('category_id');
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        $minAmount = $request->input('min_amount');
        if ($minAmount !== null && $minAmount !== '') {
            $query->where('amount', '>=', $minAmount);
        }
        
        $maxAmount = $request->input('max_amount');
        if ($maxAmount !== null && $maxAmount !== '') {
            $query->where('amount', '<=', $maxAmount);
        }
        
        $keyword = $request->input('keyword');
        if (!empty($keyword)) {
            $query->where('description', 'like', "%{$keyword}%");
        }
        
        return $query;
    }

    public function index(Request $request)
    {
        $selectedYear = $request->input('year', date('Y'));
        $selectedMonth = $request->input('month', date('m'));
        $categoryId = $request->input('category_id');
        $minAmount = $request->input('min_amount');
        $maxAmount = $request->input('max_amount');
        $keyword = $request->input('keyword');
        
        if ($request->has('year') && !$request->filled('month')) {
            $selectedMonth = null;
        }

        $query = $this->getFilteredQuery($request);
        $expenses = (clone $query)->latest()->paginate(15)->withQueryString();

        // Monthly totals – household scope applied automatically by global scope
        $monthlyQuery = Expense::query();
        if ($selectedYear) {
            $monthlyQuery->whereYear('expense_date', $selectedYear);
        }
        if ($selectedMonth) {
            $monthlyQuery->whereMonth('expense_date', $selectedMonth);
        }
        
        $driver = \Illuminate\Support\Facades\DB::getDriverName();
        $dateFormat = $driver === 'pgsql' ? "TO_CHAR(expense_date, 'YYYY-MM')" : "DATE_FORMAT(expense_date, '%Y-%m')";

        $monthlyRaw = $monthlyQuery
            ->selectRaw("$dateFormat as month, SUM(amount) as total")
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
            $catExpQuery = Expense::where('category_id', $category->id);
            $catBudgetQuery = Budget::where('category_id', $category->id);

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
            'categories',
            'selectedYear',
            'selectedMonth',
            'categoryId',
            'minAmount',
            'maxAmount',
            'keyword'
        ));
    }


    public function create()
    {
        $categories = Category::all();
        $accounts = Account::all();
        return view('expenses.create', compact('categories', 'accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'description' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request) {
            $expense = Expense::create([
                'user_id' => auth()->id(), // keeping for compatibility, but household handles the rest
                'category_id' => $request->category_id,
                'account_id' => $request->account_id,
                'amount' => $request->amount,
                'description' => $request->description,
                'expense_date' => $request->expense_date
            ]);

            $account = Account::findOrFail($request->account_id);
            $account->decrement('balance', $request->amount);

            // Log activity
            \App\Models\Activity::create([
                'household_id' => session('active_household_id'),
                'user_id' => auth()->id(),
                'action' => 'expense_created',
                'description' => auth()->user()->name . " a enregistré une dépense de " . number_format($request->amount, 0, ',', ' ') . " Ar (" . ($expense->category->name ?? 'Catégorie') . ")"
            ]);

            // Notification: Expense Created
            \App\Models\Notification::create([
                'user_id' => auth()->id(),
                'household_id' => session('active_household_id'),
                'type' => 'info',
                'message' => "Nouvelle dépense de " . number_format($request->amount, 0, ',', ' ') . " Ar (" . ($expense->category->name ?? 'Catégorie') . ")",
                'is_read' => false
            ]);

            // Budget Alerts
            $month = \Carbon\Carbon::parse($request->expense_date)->month;
            $year = \Carbon\Carbon::parse($request->expense_date)->year;

            $budget = \App\Models\Budget::where('category_id', $request->category_id)
                ->whereMonth('month', $month)
                ->whereYear('month', $year)
                ->first();

            if ($budget && $budget->amount > 0) {
                $totalExpenses = Expense::where('category_id', $request->category_id)
                    ->whereMonth('expense_date', $month)
                    ->whereYear('expense_date', $year)
                    ->sum('amount');
                
                $percent = ($totalExpenses / $budget->amount) * 100;

                if ($percent >= 100) {
                    \App\Models\Notification::create([
                        'user_id' => auth()->id(),
                        'household_id' => session('active_household_id'),
                        'type' => 'danger',
                        'message' => "Attention : Budget dépassé pour la catégorie '" . ($expense->category->name ?? '') . "' !",
                        'is_read' => false
                    ]);
                } elseif ($percent >= 80) {
                    \App\Models\Notification::create([
                        'user_id' => auth()->id(),
                        'household_id' => session('active_household_id'),
                        'type' => 'warning',
                        'message' => "Alerte : Vous avez atteint " . round($percent) . "% du budget pour la catégorie '" . ($expense->category->name ?? '') . "'.",
                        'is_read' => false
                    ]);
                }
            }
        });

        return redirect()->route('expenses.index')->with('success', 'Dépense enregistrée.');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $expense = Expense::findOrFail($id);
            if ($expense->account_id) {
                $account = Account::findOrFail($expense->account_id);
                $account->increment('balance', $expense->amount);
            }
            $expense->delete();
        });
        
        return redirect()->back()->with('success', 'Dépense supprimée.');
    }

    public function exportExcel(Request $request)
    {
        $expenses = $this->getFilteredQuery($request)->latest()->get();
        return Excel::download(new ExpensesExport($expenses), 'depenses.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $expenses = $this->getFilteredQuery($request)->latest()->get();
        $pdf = Pdf::loadView('exports.expenses_pdf', compact('expenses'));
        return $pdf->download('depenses.pdf');
    }
}
