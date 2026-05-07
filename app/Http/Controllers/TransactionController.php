<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Budget;
use App\Models\Transaction;
use App\Notifications\BudgetThresholdNotification;
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

        $sort = in_array($request->query('sort'), ['date', 'amount'], true)
            ? $request->query('sort')
            : 'date';
        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';

        $sortColumn = $sort === 'amount' ? 'amount' : 'transaction_date';

        $transactions = $request->user()->transactions()
            ->with('category')
            ->betweenDates($filters['from'], $filters['to'])
            ->ofCategory($filters['category_id'] ? (int) $filters['category_id'] : null)
            ->ofType($filters['type'])
            ->searchNote($filters['q'])
            ->orderBy($sortColumn, $direction)
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $categories = $request->user()->categories()->orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'categories', 'filters', 'sort', 'direction'));
    }

    public function store(TransactionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $category = $request->user()->categories()->where('id', $data['category_id'])->firstOrFail();

        if ($category->type !== $data['type']) {
            return back()->withErrors(['category_id' => 'Kategorija ne odgovara tipu transakcije.'])->withInput();
        }

        $tx = $request->user()->transactions()->create($data);

        if ($tx->type === Transaction::TYPE_EXPENSE) {
            $this->checkBudgetThresholds($request->user(), $tx);
        }

        return redirect()->route('transactions.index')->with('success', 'Transakcija je dodata.');
    }

    private function checkBudgetThresholds($user, Transaction $tx): void
    {
        $budget = $user->budgets()
            ->where('category_id', $tx->category_id)
            ->where('month', (int) $tx->transaction_date->format('n'))
            ->where('year', (int) $tx->transaction_date->format('Y'))
            ->first();

        if (! $budget) {
            return;
        }

        $spent = (float) $budget->spentAmount();
        $limit = (float) $budget->limit_amount;
        $pct = $limit > 0 ? ($spent / $limit) * 100 : 0;

        if ($pct >= 100 && ! $budget->notified_100) {
            $user->notify(new BudgetThresholdNotification($budget, 100, $spent));
            $budget->update(['notified_100' => true, 'notified_80' => true]);
        } elseif ($pct >= 80 && $pct < 100 && ! $budget->notified_80) {
            $user->notify(new BudgetThresholdNotification($budget, 80, $spent));
            $budget->update(['notified_80' => true]);
        }
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
        $transaction->loadMissing('category');

        return response()->json([
            'id' => $transaction->id,
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
            'category_id' => $transaction->category_id,
            'category_name' => $transaction->category->name,
            'note' => $transaction->note,
        ]);
    }
}
