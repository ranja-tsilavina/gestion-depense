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
        return view('categories.index',compact('categories'));
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