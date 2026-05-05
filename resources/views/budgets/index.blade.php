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
                <tbody class="divide-y divide-app-border">
                    @foreach ($budgets as $budget)
                        @php
                            $spent = $budget->spentAmount();
                            $pct = $budget->percentSpent();
                            $remaining = max(0, (float) $budget->limit_amount - $spent);
                        @endphp
                        <tr class="budget-row">
                            <td class="px-5 py-3" colspan="5">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-sm" style="background-color: {{ $budget->category->color }}">
                                        <i class="bi {{ $budget->category->icon ?? 'bi-tag' }}"></i>
                                    </span>
                                    <span class="font-medium budget-category flex-1">{{ $budget->category->name }}</span>
                                    <span class="text-sm tabular-nums text-app-text-muted">
                                        {{ number_format($spent, 0, ',', '.') }} / {{ number_format($budget->limit_amount, 0, ',', '.') }} RSD
                                    </span>
                                    <span class="text-sm font-semibold tabular-nums">{{ $pct }}%</span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-app-bg-soft overflow-hidden">
                                    <div class="progress-bar h-full rounded-full bg-app-positive"
                                         style="width: {{ min(100, $pct) }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>
