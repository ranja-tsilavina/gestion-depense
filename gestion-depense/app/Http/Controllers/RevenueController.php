<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Revenue;
use App\Exports\RevenuesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    private function getFilteredQuery(Request $request)
    {
        $userId = auth()->id();
        $query = Revenue::where('user_id', $userId);
        
        $selectedYear = $request->input('year');
        $selectedMonth = $request->input('month');
        if ($request->has('year') && !$request->filled('month')) {
            $selectedMonth = null;
        }

        if ($selectedYear && $selectedYear !== '') {
            $query->whereYear('revenue_date', $selectedYear);
        }
        if ($selectedMonth) {
            $query->whereMonth('revenue_date', $selectedMonth);
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
            $query->where(function($q) use ($keyword) {
                $q->where('source', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }
        
        return $query;
    }

    public function index(Request $request)
    {
        $userId = auth()->id();
        
        $selectedYear = $request->input('year', date('Y'));
        $selectedMonth = $request->input('month', date('m'));
        $minAmount = $request->input('min_amount');
        $maxAmount = $request->input('max_amount');
        $keyword = $request->input('keyword');
        
        if ($request->has('year') && !$request->filled('month')) {
            $selectedMonth = null;
        }

        $query = $this->getFilteredQuery($request);
        $revenues = $query->latest()->get();

        return view('revenues.index', compact(
            'revenues', 
            'selectedYear', 
            'selectedMonth',
            'minAmount',
            'maxAmount',
            'keyword'
        ));
    }

    public function create()
    {
        $accounts = Account::all();
        return view('revenues.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'source' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'revenue_date' => 'required|date',
            'description' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request) {
            Revenue::create([
                'user_id' => auth()->id(),
                'account_id' => $request->account_id,
                'source' => $request->source,
                'amount' => $request->amount,
                'description' => $request->description,
                'revenue_date' => $request->revenue_date
            ]);

            $account = Account::findOrFail($request->account_id);
            $account->increment('balance', $request->amount);
        });

        return redirect()->route('revenues.index')->with('success', 'Revenu enregistré.');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $revenue = Revenue::findOrFail($id);
            if ($revenue->account_id) {
                $account = Account::findOrFail($revenue->account_id);
                $account->decrement('balance', $revenue->amount);
            }
            $revenue->delete();
        });
        
        return redirect()->back()->with('success', 'Revenu supprimé.');
    }

    public function exportExcel(Request $request)
    {
        $revenues = $this->getFilteredQuery($request)->latest()->get();
        return Excel::download(new RevenuesExport($revenues), 'revenus.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $revenues = $this->getFilteredQuery($request)->latest()->get();
        $pdf = Pdf::loadView('exports.revenues_pdf', compact('revenues'));
        return $pdf->download('revenus.pdf');
    }
}
