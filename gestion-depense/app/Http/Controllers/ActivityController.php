<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with('user')
            ->where('household_id', session('active_household_id'))
            ->latest()
            ->paginate(30);

        return view('activities.index', compact('activities'));
    }
}
