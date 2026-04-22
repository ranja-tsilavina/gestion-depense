<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Household;
use App\Models\Account;

class SetHouseholdContext
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            if (!session()->has('active_household_id')) {
                $household = $user->households()->first();

                if (!$household) {
                    // Create default household
                    $household = Household::create([
                        'name' => 'Maison de ' . $user->name,
                        'owner_id' => $user->id
                    ]);
                    $household->users()->attach($user->id, ['role' => 'owner']);
                    
                    // Create default account
                    Account::create([
                        'household_id' => $household->id,
                        'user_id' => $user->id,
                        'name' => 'Espèces',
                        'balance' => 0
                    ]);
                }

                session(['active_household_id' => $household->id]);
            }

            // Ensure the user has at least one account in this household
            $activeHouseholdId = session('active_household_id');
            $hasAccount = Account::where('household_id', $activeHouseholdId)->exists();
            if (!$hasAccount) {
                Account::create([
                    'household_id' => $activeHouseholdId,
                    'user_id' => $user->id,
                    'name' => 'Compte Principal',
                    'balance' => 0
                ]);
            }
        }

        return $next($request);
    }
}
