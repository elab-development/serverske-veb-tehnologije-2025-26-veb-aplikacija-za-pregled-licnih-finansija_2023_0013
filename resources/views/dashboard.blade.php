<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold">Dashboard</h1>
    </x-slot>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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
