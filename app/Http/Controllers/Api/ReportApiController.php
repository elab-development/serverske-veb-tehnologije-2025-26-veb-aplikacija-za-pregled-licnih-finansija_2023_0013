<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $from = $request->query('from', now()->startOfMonth()->subMonth()->format('Y-m-d'));
        $to   = $request->query('to', now()->format('Y-m-d'));

        $user = $request->user();

        $summary = $user->transactions()
            ->whereBetween('transaction_date', [$from, $to])
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(fn ($txs) => [
                'category_id' => $txs->first()->category_id,
                'category'    => $txs->first()->category->name,
                'type'        => $txs->first()->category->type,
                'color'       => $txs->first()->category->color,
                'count'       => $txs->count(),
                'total'       => (float) $txs->sum('amount'),
            ])
            ->sortByDesc('total')
            ->values();

        $totalIncome = (float) $user->transactions()
            ->whereBetween('transaction_date', [$from, $to])
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = (float) $user->transactions()
            ->whereBetween('transaction_date', [$from, $to])
            ->where('type', 'expense')
            ->sum('amount');

        return response()->json([
            'data' => [
                'from'          => $from,
                'to'            => $to,
                'total_income'  => $totalIncome,
                'total_expense' => $totalExpense,
                'net'           => $totalIncome - $totalExpense,
                'summary'       => $summary,
            ],
            'message' => 'OK',
        ]);
    }
}
