<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Category;
use App\Models\Budget;

class ExpenseController extends Controller
{

    public function index()
    {
        $expenses = Expense::with('category')
                    ->where('user_id', auth()->id())
                    ->latest()
                    ->get();

        // ── Monthly totals (last 12 months) ──
        $monthlyRaw = Expense::where('user_id', auth()->id())
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
        $categoryRaw = Expense::where('user_id', auth()->id())
            ->with('category')
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
            $totalDepenses = Expense::where('user_id', auth()->id())
                                ->where('category_id', $category->id)
                                ->whereMonth('expense_date', date('m'))
                                ->whereYear('expense_date', date('Y'))
                                ->sum('amount');

            $budget = Budget::where('user_id', auth()->id())
                            ->where('category_id', $category->id)
                            ->whereMonth('month', date('m'))
                            ->whereYear('month', date('Y'))
                            ->first();

            if ($budget && $totalDepenses > $budget->amount) {
                $alertes[] = "Attention : Vous avez dépassé le budget de la catégorie '{$category->name}' !";
            }
        }

        return view('expenses.index', compact(
            'expenses',
            'monthlyLabels', 'monthlyTotals',
            'categoryLabels', 'categoryTotals',
            'alertes'
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
