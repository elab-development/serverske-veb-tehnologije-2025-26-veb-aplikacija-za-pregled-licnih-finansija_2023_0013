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

    <div class="bg-white border border-app-border rounded-xl p-6 text-center text-app-text-muted">
        Lista budzeta dolazi u narednom commit-u.
    </div>
</x-app-layout>
