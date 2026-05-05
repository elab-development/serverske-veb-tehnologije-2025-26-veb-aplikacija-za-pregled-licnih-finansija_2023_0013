@php
    $months = [
        1 => 'Januar', 2 => 'Februar', 3 => 'Mart', 4 => 'April', 5 => 'Maj', 6 => 'Jun',
        7 => 'Jul', 8 => 'Avgust', 9 => 'Septembar', 10 => 'Oktobar', 11 => 'Novembar', 12 => 'Decembar',
    ];
@endphp
<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold">Budzeti</h1>
    </x-slot>

    <form method="GET" action="{{ route('budgets.index') }}" class="bg-white border border-app-border rounded-xl p-4 mb-6 flex items-center gap-3 flex-wrap">
        <span class="text-sm text-app-text-muted">Period:</span>
        <select name="month" onchange="this.form.submit()" class="px-3 py-2 border border-app-border rounded-lg text-sm period-month">
            @foreach ($months as $num => $name)
                <option value="{{ $num }}" @selected($month === $num)>{{ $name }}</option>
            @endforeach
        </select>
        <select name="year" onchange="this.form.submit()" class="px-3 py-2 border border-app-border rounded-lg text-sm period-year">
            @for ($y = now()->year - 2; $y <= now()->year + 1; $y++)
                <option value="{{ $y }}" @selected($year === $y)>{{ $y }}</option>
            @endfor
        </select>
    </form>

    <div class="bg-white border border-app-border rounded-xl overflow-hidden">
        @if ($budgets->isEmpty())
            <div class="p-12 text-center text-app-text-muted budgets-empty">
                <i class="bi bi-piggy-bank text-4xl block mb-3"></i>
                Za izabrani period nema budzeta.
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-app-bg-soft text-app-text-muted text-xs uppercase">
                    <tr>
                        <th class="text-left px-5 py-3 font-medium">Kategorija</th>
                        <th class="text-right px-5 py-3 font-medium">Limit</th>
                        <th class="text-right px-5 py-3 font-medium">Potroseno</th>
                        <th class="text-right px-5 py-3 font-medium">Ostalo</th>
                        <th class="text-right px-5 py-3 font-medium">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-app-border">
                    @foreach ($budgets as $budget)
                        @php
                            $spent = $budget->spentAmount();
                            $pct = $budget->percentSpent();
                            $remaining = max(0, (float) $budget->limit_amount - $spent);
                        @endphp
                        <tr class="budget-row">
                            <td class="px-5 py-3">
                                <div class="inline-flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-sm" style="background-color: {{ $budget->category->color }}">
                                        <i class="bi {{ $budget->category->icon ?? 'bi-tag' }}"></i>
                                    </span>
                                    <span class="font-medium budget-category">{{ $budget->category->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-right tabular-nums">{{ number_format($budget->limit_amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right tabular-nums">{{ number_format($spent, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right tabular-nums">{{ number_format($remaining, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right font-semibold tabular-nums">{{ $pct }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>
