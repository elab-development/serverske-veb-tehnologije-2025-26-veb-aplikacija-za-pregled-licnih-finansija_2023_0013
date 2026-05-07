<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->query('from', now()->startOfMonth()->subMonth()->format('Y-m-d'));
        $to = $request->query('to', now()->format('Y-m-d'));

        $user = $request->user();

        $summary = $user->transactions()
            ->whereBetween('transaction_date', [$from, $to])
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(fn ($txs) => [
                'category' => $txs->first()->category->name,
                'type' => $txs->first()->category->type,
                'color' => $txs->first()->category->color,
                'count' => $txs->count(),
                'total' => (float) $txs->sum('amount'),
            ])
            ->sortByDesc('total')
            ->values();

        return view('reports.index', compact('from', 'to', 'summary'));
    }
}
