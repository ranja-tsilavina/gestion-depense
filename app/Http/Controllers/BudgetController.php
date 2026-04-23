<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Category;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = Budget::with('category')
            ->latest('month')
            ->get();

        // Calculate spent amount for each budget without N+1
        $budgetMonths = $budgets->pluck('month')->unique()->map(function($m) {
            return \Carbon\Carbon::parse($m);
        });

        if ($budgetMonths->isNotEmpty()) {
            $minMonth = $budgetMonths->min()->startOfMonth()->format('Y-m-d');
            $maxMonth = $budgetMonths->max()->endOfMonth()->format('Y-m-d');

            $driver = \Illuminate\Support\Facades\DB::getDriverName();
            $dateFormat = $driver === 'pgsql' ? "TO_CHAR(expense_date, 'YYYY-MM')" : "DATE_FORMAT(expense_date, '%Y-%m')";

            $expensesRaw = \App\Models\Expense::selectRaw("category_id, $dateFormat as month_str, SUM(amount) as total_spent")
                ->whereBetween('expense_date', [$minMonth, $maxMonth])
                ->groupBy('category_id', 'month_str')
                ->get()
                ->keyBy(function($item) {
                    return $item->category_id . '_' . $item->month_str;
                });

            foreach ($budgets as $budget) {
                // month is saved as '2026-04-01', we need '2026-04'
                $key = $budget->category_id . '_' . \Carbon\Carbon::parse($budget->month)->format('Y-m');
                $budget->spent = $expensesRaw[$key]->total_spent ?? 0;
                $budget->remaining = file_exists('amount') ? $budget->amount - $budget->spent : $budget->amount - $budget->spent; 
            }
        } else {
            foreach ($budgets as $budget) {
                $budget->spent = 0;
                $budget->remaining = $budget->amount;
            }
        }

        return view('budgets.index', compact('budgets'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('budgets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $month = $request->month . '-01'; // 2026-04 → 2026-04-01

        Budget::create([
            'user_id' => auth()->id(),
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'month' => $month
        ]);

        return redirect()->route('budgets.index');
    }

    public function destroy($id)
    {
        Budget::findOrFail($id)->delete();
        return redirect()->back();
    }
}
