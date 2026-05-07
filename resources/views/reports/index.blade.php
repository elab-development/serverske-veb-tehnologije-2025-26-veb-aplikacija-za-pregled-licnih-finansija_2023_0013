<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold">Izvestaji</h1>
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

    <div class="bg-white border border-app-border rounded-xl p-6 text-center text-app-text-muted">
        Izvestaji dolaze u narednom commit-u.
    </div>
</x-app-layout>
