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

        $monthIncome = (float) $user->transactions()
            ->where('type', Transaction::TYPE_INCOME)
            ->whereYear('transaction_date', now()->year)
            ->whereMonth('transaction_date', now()->month)
            ->sum('amount');

        $monthExpense = (float) $user->transactions()
            ->where('type', Transaction::TYPE_EXPENSE)
            ->whereYear('transaction_date', now()->year)
            ->whereMonth('transaction_date', now()->month)
            ->sum('amount');

        $monthSavings = $monthIncome - $monthExpense;

        $categoryExpenses = $user->transactions()
            ->where('type', Transaction::TYPE_EXPENSE)
            ->whereYear('transaction_date', now()->year)
            ->whereMonth('transaction_date', now()->month)
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(fn ($txs) => [
                'name' => $txs->first()->category->name,
                'color' => $txs->first()->category->color,
                'total' => (float) $txs->sum('amount'),
            ])
            ->values();

        return view('dashboard', compact('balance', 'monthIncome', 'monthExpense', 'monthSavings', 'categoryExpenses'));
    }
}
