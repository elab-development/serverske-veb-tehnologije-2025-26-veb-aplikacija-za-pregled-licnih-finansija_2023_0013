<?php

namespace App\Http\Controllers;

use App\Exports\TransactionsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

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

        $balanceSeries = $user->transactions()
            ->whereBetween('transaction_date', [$from, $to])
            ->orderBy('transaction_date')
            ->get(['transaction_date', 'amount', 'type'])
            ->groupBy(fn ($t) => $t->transaction_date->format('Y-m-d'))
            ->map(function ($dayTxs) {
                $income = (float) $dayTxs->where('type', 'income')->sum('amount');
                $expense = (float) $dayTxs->where('type', 'expense')->sum('amount');
                return $income - $expense;
            });

        $cumulative = 0;
        $balanceTimeline = collect();
        foreach ($balanceSeries as $date => $delta) {
            $cumulative += $delta;
            $balanceTimeline->push(['date' => $date, 'balance' => $cumulative]);
        }

        return view('reports.index', compact('from', 'to', 'summary', 'balanceTimeline'));
    }

    public function exportPdf(Request $request): Response
    {
        $from = $request->query('from', now()->startOfMonth()->subMonth()->format('Y-m-d'));
        $to = $request->query('to', now()->format('Y-m-d'));
        $user = $request->user();

        $transactions = $user->transactions()
            ->whereBetween('transaction_date', [$from, $to])
            ->with('category')
            ->orderBy('transaction_date')
            ->get();

        $summary = $transactions
            ->groupBy('category_id')
            ->map(fn ($txs) => [
                'category' => $txs->first()->category->name,
                'type' => $txs->first()->category->type,
                'count' => $txs->count(),
                'total' => (float) $txs->sum('amount'),
            ])
            ->sortByDesc('total')
            ->values();

        $pdf = Pdf::loadView('reports.pdf', compact('user', 'transactions', 'summary', 'from', 'to'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('izvestaj-' . $from . '-' . $to . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $from = $request->query('from', now()->startOfMonth()->subMonth()->format('Y-m-d'));
        $to = $request->query('to', now()->format('Y-m-d'));

        return new TransactionsExport($request->user(), $from, $to);
    }
}
