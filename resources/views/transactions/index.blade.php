<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold">Transakcije</h1>
        <button type="button" @click="$dispatch('open-tx-modal')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-app-accent hover:bg-app-accent-hov text-white rounded-lg text-sm font-medium new-transaction-btn">
            <i class="bi bi-plus-lg"></i> Nova transakcija
        </button>
    </x-slot>

    @include('transactions._modal', ['categories' => $categories])

    <form method="GET" action="{{ route('transactions.index') }}"
          x-data
          @submit="
              [...$el.querySelectorAll('input, select')].forEach(el => {
                  if (el.name !== '_token' && el.value === '') el.disabled = true;
              });
          "
          class="bg-white border border-app-border rounded-xl p-4 mb-6 grid grid-cols-1 md:grid-cols-5 gap-3">
        <div>
            <label class="block text-xs text-app-text-muted mb-1">Od</label>
            <input type="date" name="from" value="{{ $filters['from'] }}" class="w-full px-3 py-2 border border-app-border rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs text-app-text-muted mb-1">Do</label>
            <input type="date" name="to" value="{{ $filters['to'] }}" class="w-full px-3 py-2 border border-app-border rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs text-app-text-muted mb-1">Kategorija</label>
            <select name="category_id" class="w-full px-3 py-2 border border-app-border rounded-lg text-sm">
                <option value="">Sve kategorije</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected((string) $filters['category_id'] === (string) $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-app-text-muted mb-1">Tip</label>
            <select name="type" class="w-full px-3 py-2 border border-app-border rounded-lg text-sm">
                <option value="">Svi</option>
                <option value="income" @selected($filters['type'] === 'income')>Prihod</option>
                <option value="expense" @selected($filters['type'] === 'expense')>Rashod</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-app-text-muted mb-1">Pretraga napomene</label>
            <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="pretrazi napomenu..." class="w-full px-3 py-2 border border-app-border rounded-lg text-sm">
        </div>
        <div class="md:col-span-5 flex items-center justify-end gap-2 pt-2 border-t border-app-border">
            @if (array_filter($filters))
                <a href="{{ route('transactions.index') }}" class="px-4 py-2 border border-app-border rounded-lg text-sm hover:bg-app-bg-soft reset-filters">
                    <i class="bi bi-x-circle"></i> Resetuj
                </a>
            @endif
            <button type="submit" class="px-4 py-2 bg-app-accent hover:bg-app-accent-hov text-white rounded-lg text-sm font-medium">
                <i class="bi bi-funnel"></i> Primeni
            </button>
        </div>
    </form>

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
                        <th class="text-left px-5 py-3 font-medium">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'date', 'direction' => $sort === 'date' && $direction === 'desc' ? 'asc' : 'desc']) }}"
                               class="inline-flex items-center gap-1 sort-date">
                                Datum
                                @if ($sort === 'date')
                                    <i class="bi bi-arrow-{{ $direction === 'desc' ? 'down' : 'up' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th class="text-left px-5 py-3 font-medium">Kategorija</th>
                        <th class="text-right px-5 py-3 font-medium">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'amount', 'direction' => $sort === 'amount' && $direction === 'desc' ? 'asc' : 'desc']) }}"
                               class="inline-flex items-center gap-1 sort-amount">
                                Iznos
                                @if ($sort === 'amount')
                                    <i class="bi bi-arrow-{{ $direction === 'desc' ? 'down' : 'up' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th class="text-left px-5 py-3 font-medium">Napomena</th>
                        <th class="text-right px-5 py-3 font-medium">Akcije</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-app-border">
                    @foreach ($transactions as $tx)
                        <tr class="transaction-row hover:bg-app-bg-soft">
                            <td class="px-5 py-3 whitespace-nowrap">{{ $tx->transaction_date->format('d.m.Y') }}</td>
                            <td class="px-5 py-3 category-cell">
                                <div class="inline-flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-white shadow-sm category-icon" style="background-color: {{ $tx->category->color }}" title="{{ $tx->category->name }}">
                                        <i class="bi {{ $tx->category->icon ?? 'bi-tag' }}"></i>
                                    </span>
                                    <span class="font-medium">{{ $tx->category->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-right font-semibold tabular-nums {{ $tx->type === 'income' ? 'text-app-positive amount-income' : 'text-app-negative amount-expense' }}">
                                {{ $tx->type === 'income' ? '+' : '-' }}{{ number_format($tx->amount, 2, ',', '.') }} RSD
                            </td>
                            <td class="px-5 py-3 text-app-text-muted">{{ $tx->note }}</td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <button type="button" class="edit-tx-btn text-app-text-muted hover:text-app-text"
                                        @click="
                                            const r = await fetch('{{ route('transactions.show', $tx) }}', { headers: { Accept: 'application/json' } });
                                            const tx = await r.json();
                                            $dispatch('open-tx-modal', {
                                                title: 'Izmena transakcije',
                                                action: '{{ route('transactions.update', $tx) }}',
                                                method: 'PUT',
                                                ...tx,
                                            });
                                        ">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('transactions.destroy', $tx) }}" class="inline ml-2 tx-delete-form" onsubmit="return confirm('Obrisati transakciju od {{ number_format($tx->amount, 2, ',', '.') }} RSD?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-tx-btn text-app-text-muted hover:text-app-negative">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-5 py-3 border-t border-app-border flex items-center justify-between text-sm text-app-text-muted">
                <span>Ukupno: {{ $transactions->total() }}</span>
                <div>{{ $transactions->onEachSide(1)->links() }}</div>
            </div>
        @endif
    </div>
</x-app-layout>
