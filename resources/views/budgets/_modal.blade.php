@props(['expenseCategories', 'month', 'year'])

<div x-data="{
        open: false,
        action: '{{ route('budgets.store') }}',
        method: 'POST',
        title: 'Novi budzet',
        showCategory: true,
        category_id: '',
        limit_amount: '',
        month: {{ $month }},
        year: {{ $year }},
     }"
     x-show="open"
     x-on:open-budget-modal.window="
        open = true;
        title = $event.detail?.title || 'Novi budzet';
        action = $event.detail?.action || '{{ route('budgets.store') }}';
        method = $event.detail?.method || 'POST';
        showCategory = $event.detail?.showCategory ?? true;
        category_id = $event.detail?.category_id || '';
        limit_amount = $event.detail?.limit_amount || '';
        month = $event.detail?.month ?? {{ $month }};
        year = $event.detail?.year ?? {{ $year }};
     "
     @keydown.escape.window="open = false"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
    <div @click.outside="open = false"
         class="bg-white rounded-xl shadow-lg w-full max-w-md p-6"
         id="budgetModal">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold" x-text="title"></h2>
            <button @click="open = false" type="button" class="close-btn text-app-text-muted hover:text-app-text">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form :action="action" method="POST" class="space-y-4">
            @csrf
            <template x-if="method !== 'POST'">
                <input type="hidden" name="_method" :value="method">
            </template>

            <div x-show="showCategory">
                <label class="block text-sm font-medium mb-1">Kategorija (rashod)</label>
                <select name="category_id" x-model="category_id" :required="showCategory"
                        class="w-full px-3 py-2 border border-app-border rounded-lg focus:outline-none focus:border-app-accent">
                    <option value="">-- izaberi --</option>
                    @foreach ($expenseCategories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-1 text-xs text-app-negative">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Mesecni limit (RSD)</label>
                <input type="number" step="0.01" name="limit_amount" x-model="limit_amount" required
                       class="w-full px-3 py-2 border border-app-border rounded-lg focus:outline-none focus:border-app-accent">
                @error('limit_amount') <p class="mt-1 text-xs text-app-negative">{{ $message }}</p> @enderror
            </div>

            <input type="hidden" name="month" x-model="month">
            <input type="hidden" name="year" x-model="year">

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" @click="open = false" class="px-4 py-2 text-sm border border-app-border rounded-lg hover:bg-app-bg-soft">Otkazi</button>
                <button type="submit" class="px-4 py-2 text-sm bg-app-accent hover:bg-app-accent-hov text-white rounded-lg font-medium">Sacuvaj</button>
            </div>
        </form>
    </div>
</div>
