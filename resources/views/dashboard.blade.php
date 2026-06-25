<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold">Dashboard</h1>
    </x-slot>

    <div class="bg-white border border-app-border rounded-xl p-4 sm:p-5 shadow-sm mb-6">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <div class="text-xs uppercase text-app-text-muted mb-1">Nivo</div>
                <div class="text-lg font-semibold">🏆 {{ $level }}</div>
            </div>
            <div class="text-right">
                <div class="text-xs uppercase text-app-text-muted mb-1">Poeni</div>
                <div class="text-lg font-semibold tabular-nums">{{ number_format($points, 0, ',', '.') }} poena</div>
            </div>
        </div>
        <div class="mt-3">
            <div class="w-full h-1.5 rounded-full bg-app-bg-soft">
                <div class="h-full rounded-full bg-app-positive" style="width: {{ $levelProgressPercent }}%"></div>
            </div>
            @if ($nextLevelThreshold !== null)
                <div class="text-xs text-app-text-muted mt-1">
                    Jos {{ number_format($nextLevelThreshold - $points, 0, ',', '.') }} poena do sledeceg nivoa
                </div>
            @else
                <div class="text-xs text-app-text-muted mt-1">Dostigli ste najvisi nivo!</div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="kpi-card bg-white border border-app-border rounded-xl p-4 sm:p-5 shadow-sm">
            <div class="text-xs uppercase text-app-text-muted mb-1">Bilans</div>
            <div class="text-2xl font-semibold tabular-nums {{ $balance >= 0 ? 'text-app-text' : 'text-app-negative' }}">
                {{ number_format($balance, 0, ',', '.') }} <span class="text-sm text-app-text-muted">RSD</span>
            </div>
        </div>
        <div class="kpi-card bg-white border border-app-border rounded-xl p-4 sm:p-5 shadow-sm">
            <div class="text-xs uppercase text-app-text-muted mb-1">Prihodi meseca</div>
            <div class="text-2xl font-semibold tabular-nums text-app-positive">
                {{ number_format($monthIncome, 0, ',', '.') }} <span class="text-sm text-app-text-muted">RSD</span>
            </div>
        </div>
        <div class="kpi-card bg-white border border-app-border rounded-xl p-4 sm:p-5 shadow-sm">
            <div class="text-xs uppercase text-app-text-muted mb-1">Rashodi meseca</div>
            <div class="text-2xl font-semibold tabular-nums text-app-negative">
                {{ number_format($monthExpense, 0, ',', '.') }} <span class="text-sm text-app-text-muted">RSD</span>
            </div>
        </div>
        <div class="kpi-card bg-white border border-app-border rounded-xl p-4 sm:p-5 shadow-sm">
            <div class="text-xs uppercase text-app-text-muted mb-1">Ustedeli</div>
            <div class="text-2xl font-semibold tabular-nums {{ $monthSavings >= 0 ? 'text-app-text' : 'text-app-negative' }}">
                {{ number_format($monthSavings, 0, ',', '.') }} <span class="text-sm text-app-text-muted">RSD</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white border border-app-border rounded-xl p-5">
            <h2 class="text-sm font-semibold mb-3">Rashodi po kategorijama (ovaj mesec)</h2>
            <div class="h-64">
                <canvas id="categoryDonutChart"></canvas>
            </div>
        </div>
        <div class="bg-white border border-app-border rounded-xl p-5">
            <h2 class="text-sm font-semibold mb-3">Prihodi vs Rashodi (6 meseci)</h2>
            <div class="h-64">
                <canvas id="monthlyLineChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white border border-app-border rounded-xl p-5">
            <h2 class="text-sm font-semibold mb-3">Status budzeta</h2>
            @if ($activeBudgets->isEmpty())
                <div class="text-app-text-muted text-sm py-8 text-center empty-state">
                    <i class="bi bi-piggy-bank text-3xl block mb-2"></i>
                    Nemate aktivnih budzeta za ovaj mesec.
                </div>
            @else
                <div class="space-y-3 budget-status-section">
                    @foreach ($activeBudgets as $budget)
                        @php
                            $spent = $budget->spentAmount();
                            $pct = $budget->percentSpent();
                            $color = $pct >= 100 ? 'bg-app-negative' : ($pct >= 80 ? 'bg-app-warning' : 'bg-app-positive');
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-medium">{{ $budget->category->name }}</span>
                                <span class="tabular-nums text-app-text-muted">
                                    {{ number_format($spent, 0, ',', '.') }} / {{ number_format($budget->limit_amount, 0, ',', '.') }} ({{ $pct }}%)
                                </span>
                            </div>
                            <div class="w-full h-1.5 rounded-full bg-app-bg-soft">
                                <div class="h-full rounded-full {{ $color }}" style="width: {{ min(100, $pct) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white border border-app-border rounded-xl p-5">
            <h2 class="text-sm font-semibold mb-3">Poslednje transakcije</h2>
            @if ($latestTransactions->isEmpty())
                <div class="text-app-text-muted text-sm py-8 text-center empty-state">
                    <i class="bi bi-inbox text-3xl block mb-2"></i>
                    Nemate transakcija.
                </div>
            @else
                <div class="divide-y divide-app-border">
                    @foreach ($latestTransactions as $tx)
                        <div class="flex items-center gap-3 py-3 transaction-row">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-sm" style="background-color: {{ $tx->category->color }}">
                                <i class="bi {{ $tx->category->icon ?? 'bi-tag' }}"></i>
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-sm truncate">{{ $tx->category->name }}</div>
                                <div class="text-xs text-app-text-muted">{{ $tx->transaction_date->format('d.m.Y') }}</div>
                            </div>
                            <span class="text-sm font-semibold tabular-nums {{ $tx->type === 'income' ? 'text-app-positive' : 'text-app-negative' }}">
                                {{ $tx->type === 'income' ? '+' : '-' }}{{ number_format($tx->amount, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const data = @json($categoryExpenses);
            const monthly = @json($monthlySeries);

            if (typeof Chart === 'undefined') return;

            if (data.length > 0) {
                new Chart(document.getElementById('categoryDonutChart'), {
                    type: 'doughnut',
                    data: {
                        labels: data.map(d => d.name),
                        datasets: [{
                            data: data.map(d => d.total),
                            backgroundColor: data.map(d => d.color),
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'right' } },
                    },
                });
            }

            new Chart(document.getElementById('monthlyLineChart'), {
                type: 'line',
                data: {
                    labels: monthly.map(m => m.label),
                    datasets: [
                        { label: 'Prihodi', data: monthly.map(m => m.income), borderColor: '#16A34A', backgroundColor: '#16A34A20', tension: 0.3 },
                        { label: 'Rashodi', data: monthly.map(m => m.expense), borderColor: '#DC2626', backgroundColor: '#DC262620', tension: 0.3 },
                    ],
                },
                options: { responsive: true, maintainAspectRatio: false },
            });
        });
    </script>
</x-app-layout>
