<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Budget;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $userId = auth()->id();
        $currentMonth = date('m');
        $currentYear = date('Y');

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
            
            $category->current_expenses = $catExpenses;
            $category->monthly_budget = $budgetAmount;
            
            if ($budgetAmount > 0) {
                $category->budget_percentage = ($catExpenses / $budgetAmount) * 100;
            } else {
                $category->budget_percentage = 0;
            }
        }

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        Category::create([
            'name'=>$request->name
        ]);

        return redirect()->back()->with('success', 'Catégorie ajoutée avec succès.');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        // Check constraints
        $hasExpenses = Expense::where('category_id', $id)->exists();
        $hasBudgets = Budget::where('category_id', $id)->exists();
        
        if ($hasExpenses || $hasBudgets) {
            return redirect()->back()->with('error', "Impossible de supprimer la catégorie '{$category->name}' car elle est utilisée dans vos dépenses ou budgets.");
        }
        
        $category->delete();
        return redirect()->back()->with('success', "La catégorie '{$category->name}' a été supprimée avec succès.");
    }
}