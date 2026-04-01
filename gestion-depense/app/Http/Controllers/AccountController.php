<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::latest()->get();
        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'balance' => 'required|numeric'
        ]);

        Account::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'balance' => $request->balance,
        ]);

        return redirect()->route('accounts.index')->with('success', 'Compte créé avec succès.');
    }

    public function edit(Account $account)
    {
        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'balance' => 'required|numeric'
        ]);

        $account->update($request->only('name', 'balance'));

        return redirect()->route('accounts.index')->with('success', 'Compte mis à jour.');
    }

    public function destroy(Account $account)
    {
        $account->delete();
        return redirect()->route('accounts.index')->with('success', 'Compte supprimé.');
    }
}
