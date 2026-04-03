<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class HouseholdController extends Controller
{
    public function index()
    {
        $household = Household::find(session('active_household_id'));
        $members = $household ? $household->users()->withPivot('role')->get() : collect();
        return view('households.index', compact('household', 'members'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $household = Household::create([
            'name'     => $request->name,
            'owner_id' => auth()->id()
        ]);

        $household->users()->attach(auth()->id(), ['role' => 'owner']);
        session(['active_household_id' => $household->id]);

        return redirect()->route('households.index')->with('success', 'Foyer créé avec succès.');
    }

    public function members()
    {
        $household = Household::findOrFail(session('active_household_id'));
        $members = $household->users()->withPivot('role')->get();
        return view('households.members', compact('household', 'members'));
    }

    public function addMember(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        $household = Household::findOrFail(session('active_household_id'));

        $newUser = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $household->users()->attach($newUser->id, ['role' => 'member']);

        return redirect()->route('households.members')->with('success', "Membre {$newUser->name} créé et ajouté au foyer.");
    }

    public function removeMember(User $user)
    {
        $household = Household::findOrFail(session('active_household_id'));

        if ($household->owner_id === $user->id) {
            return redirect()->route('households.members')->with('error', 'Impossible de retirer le propriétaire.');
        }

        $household->users()->detach($user->id);

        return redirect()->route('households.members')->with('success', 'Membre retiré du foyer.');
    }

    public function switch($id)
    {
        $household = auth()->user()->households()->findOrFail($id);
        session(['active_household_id' => $household->id]);
        return redirect()->route('dashboard');
    }
}
