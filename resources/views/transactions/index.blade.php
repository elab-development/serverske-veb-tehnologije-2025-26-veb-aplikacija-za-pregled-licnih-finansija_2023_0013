<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold">Transakcije</h1>
    </x-slot>

    <div class="bg-white border border-app-border rounded-xl overflow-hidden">
        @if ($transactions->isEmpty())
            <div class="p-12 text-center text-app-text-muted">
                <i class="bi bi-inbox text-4xl block mb-3"></i>
                Jos uvek nemate transakcija.
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-app-bg-soft text-app-text-muted text-xs uppercase">
                    <tr>
                        <th class="text-left px-5 py-3 font-medium">Datum</th>
                        <th class="text-left px-5 py-3 font-medium">Kategorija</th>
                        <th class="text-right px-5 py-3 font-medium">Iznos</th>
                        <th class="text-left px-5 py-3 font-medium">Napomena</th>
                        <th class="text-right px-5 py-3 font-medium">Akcije</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-app-border">
                    @foreach ($transactions as $tx)
                        <tr class="transaction-row hover:bg-app-bg-soft">
                            <td class="px-5 py-3 whitespace-nowrap">{{ $tx->transaction_date->format('d.m.Y') }}</td>
                            <td class="px-5 py-3">
                                <div class="inline-flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-sm" style="background-color: {{ $tx->category->color }}">
                                        <i class="bi {{ $tx->category->icon ?? 'bi-tag' }}"></i>
                                    </span>
                                    <span>{{ $tx->category->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-right font-semibold tabular-nums">
                                {{ number_format($tx->amount, 2, ',', '.') }} RSD
                            </td>
                            <td class="px-5 py-3 text-app-text-muted">{{ $tx->note }}</td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <i class="bi bi-pencil text-app-text-muted"></i>
                                <i class="bi bi-trash text-app-text-muted ml-2"></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>
