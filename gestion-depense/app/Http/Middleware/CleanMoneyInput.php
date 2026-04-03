<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CleanMoneyInput
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('amount') && is_string($request->amount)) {
            // Remove spaces from the amount string before validation/saving
            $cleaned = str_replace(' ', '', $request->amount);
            $request->merge(['amount' => $cleaned]);
        }
        return $next($request);
    }
}
