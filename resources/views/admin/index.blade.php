<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold">Admin panel</h1>
    </x-slot>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6 admin-stats">
        <div class="bg-white border border-app-border rounded-xl p-4 stat-card">
            <div class="text-xs uppercase text-app-text-muted mb-1">Korisnika</div>
            <div class="text-2xl font-semibold tabular-nums">{{ $totalUsers }}</div>
        </div>
        <div class="bg-white border border-app-border rounded-xl p-4 stat-card">
            <div class="text-xs uppercase text-app-text-muted mb-1">Transakcija</div>
            <div class="text-2xl font-semibold tabular-nums">{{ $totalTransactions }}</div>
        </div>
        <div class="bg-white border border-app-border rounded-xl p-4 stat-card">
            <div class="text-xs uppercase text-app-text-muted mb-1">Ukupno prihodi</div>
            <div class="text-xl font-semibold tabular-nums text-app-positive">{{ number_format($totalIncome, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white border border-app-border rounded-xl p-4 stat-card">
            <div class="text-xs uppercase text-app-text-muted mb-1">Ukupno rashodi</div>
            <div class="text-xl font-semibold tabular-nums text-app-negative">{{ number_format($totalExpense, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="bg-white border border-app-border rounded-xl overflow-hidden mb-6 admin-users">
        <div class="px-5 py-3 border-b border-app-border bg-app-bg-soft">
            <h2 class="text-sm font-semibold">Korisnici</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="text-app-text-muted text-xs uppercase">
                <tr>
                    <th class="text-left px-5 py-2 font-medium">Ime</th>
                    <th class="text-left px-5 py-2 font-medium">Email</th>
                    <th class="text-left px-5 py-2 font-medium">Registrovan</th>
                    <th class="text-right px-5 py-2 font-medium">Br. tx</th>
                    <th class="text-left px-5 py-2 font-medium">Status</th>
                    <th class="text-right px-5 py-2 font-medium">Akcija</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-app-border">
                @foreach ($users as $u)
                    <tr class="user-row">
                        <td class="px-5 py-2 font-medium">{{ $u->name }}</td>
                        <td class="px-5 py-2 text-app-text-muted">{{ $u->email }}</td>
                        <td class="px-5 py-2 text-app-text-muted">{{ $u->created_at->format('d.m.Y') }}</td>
                        <td class="px-5 py-2 text-right tabular-nums">{{ $u->transactions_count }}</td>
                        <td class="px-5 py-2">
                            <span class="inline-block px-2 py-0.5 rounded text-xs {{ $u->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} user-status">
                                {{ $u->is_active ? 'Aktivan' : 'Deaktiviran' }}
                            </span>
                        </td>
                        <td class="px-5 py-2 text-right">
                            @if ($u->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.toggle', $u) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-app-accent hover:underline toggle-active-btn">
                                        {{ $u->is_active ? 'Deaktiviraj' : 'Reaktiviraj' }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
