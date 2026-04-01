<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function index()
    {
        $transfers = Transfer::with(['fromAccount', 'toAccount'])->latest()->get();
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
            'to_account_id' => 'required|exists:accounts,id|different:from_account_id',
            'amount' => 'required|numeric|min:0.01',
            'transfer_date' => 'required|date',
            'description' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request) {
            $transfer = Transfer::create([
                'user_id' => auth()->id(),
                'from_account_id' => $request->from_account_id,
                'to_account_id' => $request->to_account_id,
                'amount' => $request->amount,
                'transfer_date' => $request->transfer_date,
                'description' => $request->description
            ]);

            // Update balances
            $from = Account::findOrFail($request->from_account_id);
            $to = Account::findOrFail($request->to_account_id);

            $from->decrement('balance', $request->amount);
            $to->increment('balance', $request->amount);
        });

        return redirect()->route('transfers.index')->with('success', 'Transfert effectué avec succès.');
    }
}
