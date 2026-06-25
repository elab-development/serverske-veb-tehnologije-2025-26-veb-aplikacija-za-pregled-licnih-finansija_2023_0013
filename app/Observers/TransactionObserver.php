<?php

namespace App\Observers;

use App\Models\Budget;
use App\Models\Transaction;

class TransactionObserver
{
    public function created(Transaction $transaction): void
    {
        $transaction->user->increment('points', 5);

        if ($transaction->type !== Transaction::TYPE_EXPENSE) {
            return;
        }

        $budget = Budget::where('user_id', $transaction->user_id)
            ->where('category_id', $transaction->category_id)
            ->where('month', $transaction->transaction_date->month)
            ->where('year', $transaction->transaction_date->year)
            ->first();

        if ($budget && $budget->percentSpent() < 80) {
            $transaction->user->increment('points', 20);
        }
    }
}
