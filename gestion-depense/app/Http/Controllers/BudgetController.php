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
            ->where('user_id', auth()->id())
            ->latest('month')
            ->get();

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
