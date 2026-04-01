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
        $userId = auth()->id();
        $query = Expense::with('category')->where('user_id', $userId);
        
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
        $userId = auth()->id();
        
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
            Expense::create([
                'user_id' => auth()->id(),
                'category_id' => $request->category_id,
                'account_id' => $request->account_id,
                'amount' => $request->amount,
                'description' => $request->description,
                'expense_date' => $request->expense_date
            ]);

            $account = Account::findOrFail($request->account_id);
            $account->decrement('balance', $request->amount);
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
