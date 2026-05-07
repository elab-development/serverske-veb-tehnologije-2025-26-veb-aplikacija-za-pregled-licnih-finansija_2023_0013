<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $totalIncome = (float) $user->transactions()
            ->where('type', Transaction::TYPE_INCOME)
            ->sum('amount');

        $totalExpense = (float) $user->transactions()
            ->where('type', Transaction::TYPE_EXPENSE)
            ->sum('amount');

        $balance = $totalIncome - $totalExpense;

        return view('dashboard', compact('balance'));
    }
}
