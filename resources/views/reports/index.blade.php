<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold">Izvestaji</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.export.pdf', ['from' => $from, 'to' => $to]) }}"
               class="inline-flex items-center gap-2 px-4 py-2 border border-app-border rounded-lg text-sm hover:bg-app-bg-soft export-pdf-btn">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
        </div>
    </x-slot>

    <form method="GET" action="{{ route('reports.index') }}" class="bg-white border border-app-border rounded-xl p-4 mb-6 flex items-end gap-3 flex-wrap report-filter">
        <div>
            <label class="block text-xs text-app-text-muted mb-1">Od</label>
            <input type="date" name="from" value="{{ $from }}" required class="px-3 py-2 border border-app-border rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs text-app-text-muted mb-1">Do</label>
            <input type="date" name="to" value="{{ $to }}" required class="px-3 py-2 border border-app-border rounded-lg text-sm">
        </div>
        <button type="submit" class="px-4 py-2 bg-app-accent hover:bg-app-accent-hov text-white rounded-lg text-sm font-medium apply-report">
            <i class="bi bi-funnel"></i> Primeni
        </button>
    </form>

    @if ($summary->isNotEmpty())
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white border border-app-border rounded-xl p-5">
                <h2 class="text-sm font-semibold mb-3">Po kategorijama</h2>
                <div class="h-72">
                    <canvas id="categoryBarChart"></canvas>
                </div>
            </div>
            <div class="bg-white border border-app-border rounded-xl p-5">
                <h2 class="text-sm font-semibold mb-3">Kretanje bilansa</h2>
                <div class="h-72">
                    <canvas id="balanceLineChart"></canvas>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white border border-app-border rounded-xl overflow-hidden mb-6 report-summary">
        <div class="px-5 py-3 border-b border-app-border bg-app-bg-soft">
            <h2 class="text-sm font-semibold">Rezime po kategorijama</h2>
        </div>
        @if ($summary->isEmpty())
            <div class="p-12 text-center text-app-text-muted">Za izabrani period nema transakcija.</div>
        @else
            <table class="w-full text-sm">
                <thead class="text-app-text-muted text-xs uppercase">
                    <tr>
                        <th class="text-left px-5 py-2 font-medium">Kategorija</th>
                        <th class="text-left px-5 py-2 font-medium">Tip</th>
                        <th class="text-right px-5 py-2 font-medium">Br. tx</th>
                        <th class="text-right px-5 py-2 font-medium">Ukupno</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-app-border">
                    @foreach ($summary as $row)
                        <tr class="report-row">
                            <td class="px-5 py-2">
                                <span class="inline-flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full" style="background-color: {{ $row['color'] }}"></span>
                                    {{ $row['category'] }}
                                </span>
                            </td>
                            <td class="px-5 py-2 {{ $row['type'] === 'income' ? 'text-app-positive' : 'text-app-negative' }}">
                                {{ $row['type'] === 'income' ? 'Prihod' : 'Rashod' }}
                            </td>
                            <td class="px-5 py-2 text-right tabular-nums">{{ $row['count'] }}</td>
                            <td class="px-5 py-2 text-right font-semibold tabular-nums">{{ number_format($row['total'], 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const summary = @json($summary);
            if (typeof Chart === 'undefined' || summary.length === 0) return;

            new Chart(document.getElementById('categoryBarChart'), {
                type: 'bar',
                data: {
                    labels: summary.map(r => r.category),
                    datasets: [{
                        label: 'Iznos',
                        data: summary.map(r => r.total),
                        backgroundColor: summary.map(r => r.color),
                    }],
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } },
            });

            const timeline = @json($balanceTimeline);
            if (timeline.length > 0) {
                new Chart(document.getElementById('balanceLineChart'), {
                    type: 'line',
                    data: {
                        labels: timeline.map(t => t.date),
                        datasets: [{
                            label: 'Bilans',
                            data: timeline.map(t => t.balance),
                            borderColor: '#2563EB',
                            backgroundColor: '#2563EB20',
                            tension: 0.3,
                        }],
                    },
                    options: { responsive: true, maintainAspectRatio: false },
                });
            }
        });
    </script>
</x-app-layout>
