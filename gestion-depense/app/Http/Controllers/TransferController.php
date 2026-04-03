<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Activity;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function index()
    {
        $transfers = Transfer::with(['fromAccount', 'toAccount', 'creator'])->latest()->paginate(15);
        return view('transfers.index', compact('transfers'));
    }

    public function create()
    {
        $accounts = Account::all();
        return view('transfers.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id'   => 'required|exists:accounts,id|different:from_account_id',
            'amount'          => 'required|numeric|min:0.01',
            'transfer_date'   => 'required|date',
            'description'     => 'nullable|string'
        ]);

        DB::transaction(function () use ($request) {
            $from = Account::findOrFail($request->from_account_id);
            $to   = Account::findOrFail($request->to_account_id);

            Transfer::create([
                'household_id'    => session('active_household_id'),
                'user_id'         => auth()->id(),
                'from_account_id' => $request->from_account_id,
                'to_account_id'   => $request->to_account_id,
                'amount'          => $request->amount,
                'transfer_date'   => $request->transfer_date,
                'description'     => $request->description
            ]);

            $from->decrement('balance', $request->amount);
            $to->increment('balance', $request->amount);

            Activity::create([
                'household_id' => session('active_household_id'),
                'user_id'      => auth()->id(),
                'action'       => 'transfer_created',
                'description'  => auth()->user()->name . " a effectué un virement de "
                                  . number_format($request->amount, 0, ',', ' ')
                                  . " Ar ({$from->name} → {$to->name})"
            ]);
        });

        return redirect()->route('transfers.index')->with('success', 'Transfert effectué avec succès.');
    }
}
