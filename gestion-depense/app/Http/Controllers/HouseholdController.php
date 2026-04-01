<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\User;
use Illuminate\Http\Request;

class HouseholdController extends Controller
{
    public function index()
    {
        $household = Household::find(session('active_household_id'));
        $members = $household->users;
        return view('households.index', compact('household', 'members'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $household = Household::create([
            'name' => $request->name,
            'owner_id' => auth()->id()
        ]);

        $household->users()->attach(auth()->id(), ['role' => 'owner']);

        session(['active_household_id' => $household->id]);

        return redirect()->back()->with('success', 'Maison créée.');
    }

    public function addMember(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        
        $household = Household::findOrFail(session('active_household_id'));
        $userToAdd = User::where('email', $request->email)->first();

        if ($household->users()->where('user_id', $userToAdd->id)->exists()) {
            return redirect()->back()->with('error', 'L\'utilisateur est déjà membre.');
        }

        $household->users()->attach($userToAdd->id, ['role' => 'member']);

        return redirect()->back()->with('success', 'Membre ajouté.');
    }

    public function switch($id)
    {
        $household = auth()->user()->households()->findOrFail($id);
        session(['active_household_id' => $household->id]);
        return redirect()->route('dashboard');
    }
}
