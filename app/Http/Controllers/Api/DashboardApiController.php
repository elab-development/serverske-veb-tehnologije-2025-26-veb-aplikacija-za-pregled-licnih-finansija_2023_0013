<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $totalIncome  = (float) $user->transactions()->where('type', Transaction::TYPE_INCOME)->sum('amount');
        $totalExpense = (float) $user->transactions()->where('type', Transaction::TYPE_EXPENSE)->sum('amount');
        $balance      = $totalIncome - $totalExpense;

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

        $categoryExpenses = $user->transactions()
            ->where('type', Transaction::TYPE_EXPENSE)
            ->whereYear('transaction_date', now()->year)
            ->whereMonth('transaction_date', now()->month)
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(fn ($txs) => [
                'category_id'   => $txs->first()->category_id,
                'category_name' => $txs->first()->category->name,
                'color'         => $txs->first()->category->color,
                'total'         => (float) $txs->sum('amount'),
            ])
            ->values();

        return response()->json([
            'data' => [
                'balance'                => $balance,
                'total_income'           => $totalIncome,
                'total_expense'          => $totalExpense,
                'month_income'           => $monthIncome,
                'month_expense'          => $monthExpense,
                'month_savings'          => $monthIncome - $monthExpense,
                'category_expenses'      => $categoryExpenses,
                'points'                 => $user->points,
                'level'                  => $user->level,
                'next_level_threshold'   => $user->next_level_threshold,
                'level_progress_percent' => $user->level_progress_percent,
            ],
            'message' => 'OK',
        ]);
    }
}
