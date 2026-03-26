<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Revenue;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        
        $selectedYear = $request->input('year', date('Y'));
        $selectedMonth = $request->input('month', date('m'));
        
        if ($request->has('year') && !$request->filled('month')) {
            $selectedMonth = null;
        }

        $query = Revenue::where('user_id', $userId);
        
        if ($selectedYear) {
            $query->whereYear('revenue_date', $selectedYear);
        }
        if ($selectedMonth) {
            $query->whereMonth('revenue_date', $selectedMonth);
        }

        $revenues = $query->latest()->get();

        return view('revenues.index', compact('revenues', 'selectedYear', 'selectedMonth'));
    }

    public function create()
    {
        return view('revenues.create');
    }

    public function store(Request $request)
    {
        Revenue::create([
            'user_id' => auth()->id(),
            'source' => $request->source,
            'amount' => $request->amount,
            'description' => $request->description,
            'revenue_date' => $request->revenue_date
        ]);

        return redirect()->route('revenues.index');
    }

    public function destroy($id)
    {
        Revenue::findOrFail($id)->delete();
        return redirect()->back();
    }
}
