<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'from' => $request->query('from'),
            'to' => $request->query('to'),
            'category_id' => $request->query('category_id'),
            'type' => $request->query('type'),
            'q' => $request->query('q'),
        ];

        $transactions = $request->user()->transactions()
            ->with('category')
            ->betweenDates($filters['from'], $filters['to'])
            ->ofCategory($filters['category_id'] ? (int) $filters['category_id'] : null)
            ->ofType($filters['type'])
            ->searchNote($filters['q'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $categories = $request->user()->categories()->orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'categories', 'filters'));
    }

    public function store(TransactionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $category = $request->user()->categories()->where('id', $data['category_id'])->firstOrFail();

        if ($category->type !== $data['type']) {
            return back()->withErrors(['category_id' => 'Kategorija ne odgovara tipu transakcije.'])->withInput();
        }

        $request->user()->transactions()->create($data);

        return redirect()->route('transactions.index')->with('success', 'Transakcija je dodata.');
    }

    public function update(TransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        Gate::authorize('update', $transaction);

        $data = $request->validated();
        $category = $request->user()->categories()->where('id', $data['category_id'])->firstOrFail();

        if ($category->type !== $data['type']) {
            return back()->withErrors(['category_id' => 'Kategorija ne odgovara tipu transakcije.'])->withInput();
        }

        $transaction->update($data);

        return redirect()->route('transactions.index')->with('success', 'Transakcija je izmenjena.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        Gate::authorize('delete', $transaction);

        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transakcija je obrisana.');
    }

    public function show(Transaction $transaction)
    {
        Gate::authorize('view', $transaction);

        return response()->json([
            'id' => $transaction->id,
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
            'category_id' => $transaction->category_id,
            'note' => $transaction->note,
        ]);
    }
}
