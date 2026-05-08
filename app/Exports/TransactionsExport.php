<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithStyles, Responsable
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
            Date::PHPToExcel($transaction->transaction_date),
            $transaction->type === 'income' ? 'Prihod' : 'Rashod',
            $transaction->category->name,
            (float) $transaction->amount,
            $transaction->note,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => 'dd.mm.yyyy',
            'D' => '#,##0.00 "RSD"',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F7F8FA']]],
        ];
    }
}
