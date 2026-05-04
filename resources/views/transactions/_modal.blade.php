@props(['categories'])

<div x-data="{ open: false }"
     x-show="open"
     x-on:open-tx-modal.window="open = true"
     @keydown.escape.window="open = false"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
    <div @click.outside="open = false"
         class="bg-white rounded-xl shadow-lg w-full max-w-md p-6"
         id="newTransactionModal">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Nova transakcija</h2>
            <button @click="open = false" type="button" class="close-btn text-app-text-muted hover:text-app-text">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('transactions.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Iznos (RSD)</label>
                <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" required
                       class="w-full px-3 py-2 border border-app-border rounded-lg focus:outline-none focus:border-app-accent">
                @error('amount') <p class="mt-1 text-xs text-app-negative">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Datum</label>
                <input type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required
                       class="w-full px-3 py-2 border border-app-border rounded-lg focus:outline-none focus:border-app-accent">
                @error('transaction_date') <p class="mt-1 text-xs text-app-negative">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Kategorija</label>
                <select name="category_id" required
                        class="w-full px-3 py-2 border border-app-border rounded-lg focus:outline-none focus:border-app-accent">
                    <option value="">-- izaberi --</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-1 text-xs text-app-negative">{{ $message }}</p> @enderror
            </div>

            <input type="hidden" name="type" value="expense">

            <div>
                <label class="block text-sm font-medium mb-1">Napomena</label>
                <textarea name="note" rows="2" class="w-full px-3 py-2 border border-app-border rounded-lg focus:outline-none focus:border-app-accent">{{ old('note') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" @click="open = false" class="px-4 py-2 text-sm border border-app-border rounded-lg hover:bg-app-bg-soft">Otkazi</button>
                <button type="submit" class="px-4 py-2 text-sm bg-app-accent hover:bg-app-accent-hov text-white rounded-lg font-medium">Sacuvaj</button>
            </div>
        </form>
    </div>
</div>
