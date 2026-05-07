<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold">Dashboard</h1>
    </x-slot>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="kpi-card bg-white border border-app-border rounded-xl p-5">
            <div class="text-xs uppercase text-app-text-muted mb-1">Bilans</div>
            <div class="text-2xl font-semibold tabular-nums {{ $balance >= 0 ? 'text-app-text' : 'text-app-negative' }}">
                {{ number_format($balance, 0, ',', '.') }} <span class="text-sm text-app-text-muted">RSD</span>
            </div>
        </div>
        <div class="kpi-card bg-white border border-app-border rounded-xl p-5">
            <div class="text-xs uppercase text-app-text-muted mb-1">Prihodi meseca</div>
            <div class="text-2xl font-semibold tabular-nums text-app-positive">
                {{ number_format($monthIncome, 0, ',', '.') }} <span class="text-sm text-app-text-muted">RSD</span>
            </div>
        </div>
        <div class="kpi-card bg-white border border-app-border rounded-xl p-5">
            <div class="text-xs uppercase text-app-text-muted mb-1">Rashodi meseca</div>
            <div class="text-2xl font-semibold tabular-nums text-app-negative">
                {{ number_format($monthExpense, 0, ',', '.') }} <span class="text-sm text-app-text-muted">RSD</span>
            </div>
        </div>
        <div class="kpi-card bg-white border border-app-border rounded-xl p-5">
            <div class="text-xs uppercase text-app-text-muted mb-1">Ustedeli</div>
            <div class="text-2xl font-semibold tabular-nums {{ $monthSavings >= 0 ? 'text-app-text' : 'text-app-negative' }}">
                {{ number_format($monthSavings, 0, ',', '.') }} <span class="text-sm text-app-text-muted">RSD</span>
            </div>
        </div>
    </div>
</x-app-layout>
