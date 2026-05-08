<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Category;
use App\Models\Account;
use App\Models\Budget;
use App\Models\Activity;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * ExpenseSyncController
 *
 * Handles API-based expense creation used by the PWA offline sync system.
 * Authentication is done via session cookie (same as web routes).
 * CSRF protection is applied via the 'web' middleware group.
 */
class ExpenseSyncController extends Controller
{
    /**
     * GET /api/expenses/form-data
     * Returns categories and accounts needed to render the offline form.
     */
    public function formData(Request $request)
    {
        $categories = Category::select('id', 'name')->get();
        $accounts   = Account::select('id', 'name', 'balance')->get();

        return response()->json([
            'categories' => $categories,
            'accounts'   => $accounts,
            'csrf_token' => csrf_token(),
        ]);
    }

    /**
     * POST /api/expenses/sync
     * Store an expense submitted by the PWA background sync queue.
     *
     * Accepts the same payload as the web form.
     * Returns JSON so the Service Worker can confirm success.
     */
    public function sync(Request $request)
    {
        $validated = $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'account_id'   => 'required|exists:accounts,id',
            'amount'       => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'description'  => 'nullable|string|max:255',
        ]);

        // Sanitize amount (remove spaces if user submitted formatted number)
        $validated['amount'] = (float) str_replace([' ', "\u{202F}"], '', $validated['amount']);

        try {
            DB::transaction(function () use ($validated, $request) {
                $expense = Expense::create([
                    'user_id'      => auth()->id(),
                    'category_id'  => $validated['category_id'],
                    'account_id'   => $validated['account_id'],
                    'amount'       => $validated['amount'],
                    'description'  => $validated['description'] ?? null,
                    'expense_date' => $validated['expense_date'],
                ]);

                // Debit account
                Account::findOrFail($validated['account_id'])
                    ->decrement('balance', $validated['amount']);

                $householdId = session('active_household_id');

                // Activity log
                Activity::create([
                    'household_id' => $householdId,
                    'user_id'      => auth()->id(),
                    'action'       => 'expense_created',
                    'description'  => auth()->user()->name
                        . ' a enregistré une dépense de '
                        . number_format($validated['amount'], 0, ',', ' ')
                        . ' Ar ('
                        . ($expense->category->name ?? 'Catégorie')
                        . ') [sync hors-ligne]',
                ]);

                // Info notification
                Notification::create([
                    'user_id'      => auth()->id(),
                    'household_id' => $householdId,
                    'type'         => 'info',
                    'message'      => 'Dépense synchronisée : '
                        . number_format($validated['amount'], 0, ',', ' ')
                        . ' Ar ('
                        . ($expense->category->name ?? 'Catégorie') . ')',
                    'is_read'      => false,
                ]);

                // Budget alerts
                $month  = Carbon::parse($validated['expense_date'])->month;
                $year   = Carbon::parse($validated['expense_date'])->year;
                $budget = Budget::where('category_id', $validated['category_id'])
                    ->whereMonth('month', $month)
                    ->whereYear('month', $year)
                    ->first();

                if ($budget && $budget->amount > 0) {
                    $totalExpenses = Expense::where('category_id', $validated['category_id'])
                        ->whereMonth('expense_date', $month)
                        ->whereYear('expense_date', $year)
                        ->sum('amount');

                    $percent = ($totalExpenses / $budget->amount) * 100;

                    if ($percent >= 100) {
                        Notification::create([
                            'user_id'      => auth()->id(),
                            'household_id' => $householdId,
                            'type'         => 'danger',
                            'message'      => "Budget dépassé pour '" . ($expense->category->name ?? '') . "' !",
                            'is_read'      => false,
                        ]);
                    } elseif ($percent >= 80) {
                        Notification::create([
                            'user_id'      => auth()->id(),
                            'household_id' => $householdId,
                            'type'         => 'warning',
                            'message'      => "Vous avez atteint " . round($percent) . "% du budget pour '"
                                . ($expense->category->name ?? '') . "'.",
                            'is_read'      => false,
                        ]);
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Dépense synchronisée avec succès.',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur lors de la synchronisation.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
