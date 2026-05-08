<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, Responsable
{
    use Exportable;

    private string $fileName = 'transakcije.xlsx';

    public function __construct(
        public readonly User $user,
        public readonly string $from,
        public readonly string $to,
    ) {
        $this->fileName = "transakcije-{$from}-{$to}.xlsx";
    }

    public function collection()
    {
        return $this->user->transactions()
            ->whereBetween('transaction_date', [$this->from, $this->to])
            ->with('category')
            ->orderBy('transaction_date')
            ->get();
    }

    public function headings(): array
    {
        return ['Datum', 'Tip', 'Kategorija', 'Iznos', 'Napomena'];
    }

    public function map($transaction): array
    {
        return [
            $transaction->transaction_date->format('d.m.Y'),
            $transaction->type === 'income' ? 'Prihod' : 'Rashod',
            $transaction->category->name,
            (float) $transaction->amount,
            $transaction->note,
        ];
    }
}
