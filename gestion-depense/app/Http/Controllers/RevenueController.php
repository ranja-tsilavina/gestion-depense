<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Revenue;

class RevenueController extends Controller
{
    public function index()
    {
        $revenues = Revenue::where('user_id', auth()->id())->latest()->get();
        return view('revenues.index', compact('revenues'));
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
