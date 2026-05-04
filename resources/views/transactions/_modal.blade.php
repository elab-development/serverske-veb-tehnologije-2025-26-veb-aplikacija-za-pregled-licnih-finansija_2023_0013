@props(['categories'])

@php
    $catsByType = $categories->groupBy('type');
@endphp

<div x-data="{
        open: false,
        type: 'expense',
        action: '{{ route('transactions.store') }}',
        method: 'POST',
        title: 'Nova transakcija',
        amount: '',
        date: '{{ date('Y-m-d') }}',
        category_id: '',
        note: '',
        cats: @js($catsByType),
     }"
     x-show="open"
     x-on:open-tx-modal.window="
        open = true;
        type = $event.detail?.type || 'expense';
        action = $event.detail?.action || '{{ route('transactions.store') }}';
        method = $event.detail?.method || 'POST';
        title = $event.detail?.title || 'Nova transakcija';
        amount = $event.detail?.amount || '';
        date = $event.detail?.date || '{{ date('Y-m-d') }}';
        category_id = $event.detail?.category_id || '';
        note = $event.detail?.note || '';
     "
     @keydown.escape.window="open = false"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
    <div @click.outside="open = false"
         class="bg-white rounded-xl shadow-lg w-full max-w-md p-6"
         id="newTransactionModal">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold" x-text="title"></h2>
            <button @click="open = false" type="button" class="close-btn text-app-text-muted hover:text-app-text">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form :action="action" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" :value="method">

            <div>
                <label class="block text-sm font-medium mb-2">Tip</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="border border-app-border rounded-lg px-3 py-2 cursor-pointer flex items-center gap-2"
                           :class="type === 'expense' ? 'border-app-accent bg-blue-50' : ''">
                        <input type="radio" name="type" value="expense" x-model="type" @change="category_id = (cats['expense'] || [])[0]?.id || ''" class="text-app-negative">
                        <span class="text-sm font-medium">Rashod</span>
                    </label>
                    <label class="border border-app-border rounded-lg px-3 py-2 cursor-pointer flex items-center gap-2"
                           :class="type === 'income' ? 'border-app-accent bg-blue-50' : ''">
                        <input type="radio" name="type" value="income" x-model="type" @change="category_id = (cats['income'] || [])[0]?.id || ''" class="text-app-positive">
                        <span class="text-sm font-medium">Prihod</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Iznos (RSD)</label>
                <input type="number" step="0.01" name="amount" x-model="amount" required
                       class="w-full px-3 py-2 border border-app-border rounded-lg focus:outline-none focus:border-app-accent">
                @error('amount') <p class="mt-1 text-xs text-app-negative">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Datum</label>
                <input type="date" name="transaction_date" x-model="date" :max="new Date().toISOString().slice(0,10)" required
                       class="w-full px-3 py-2 border border-app-border rounded-lg focus:outline-none focus:border-app-accent">
                @error('transaction_date') <p class="mt-1 text-xs text-app-negative">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Kategorija</label>
                <select name="category_id" x-model="category_id" required
                        class="w-full px-3 py-2 border border-app-border rounded-lg focus:outline-none focus:border-app-accent">
                    <option value="">-- izaberi --</option>
                    <template x-for="c in (cats[type] || [])" :key="c.id">
                        <option :value="c.id" x-text="c.name"></option>
                    </template>
                </select>
                @error('category_id') <p class="mt-1 text-xs text-app-negative">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Napomena</label>
                <textarea name="note" rows="2" x-model="note" class="w-full px-3 py-2 border border-app-border rounded-lg focus:outline-none focus:border-app-accent"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" @click="open = false" class="px-4 py-2 text-sm border border-app-border rounded-lg hover:bg-app-bg-soft">Otkazi</button>
                <button type="submit" class="px-4 py-2 text-sm bg-app-accent hover:bg-app-accent-hov text-white rounded-lg font-medium">Sacuvaj</button>
            </div>
        </form>
    </div>
</div>
