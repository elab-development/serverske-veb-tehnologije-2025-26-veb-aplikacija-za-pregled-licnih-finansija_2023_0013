@props(['category', 'action', 'method' => 'POST'])

<form method="POST" action="{{ $action }}" class="space-y-4">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div>
        <label class="block text-sm font-medium mb-1">Naziv</label>
        <input type="text" name="name" value="{{ old('name', $category->name) }}" required
               class="w-full px-3 py-2 border border-app-border rounded-lg focus:outline-none focus:border-app-accent">
        @error('name') <p class="mt-1 text-xs text-app-negative">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Tip</label>
        <select name="type" required
                class="w-full px-3 py-2 border border-app-border rounded-lg focus:outline-none focus:border-app-accent">
            <option value="income" @selected(old('type', $category->type) === 'income')>Prihod</option>
            <option value="expense" @selected(old('type', $category->type) === 'expense')>Rashod</option>
        </select>
        @error('type') <p class="mt-1 text-xs text-app-negative">{{ $message }}</p> @enderror
    </div>

    <div x-data="{ color: '{{ old('color', $category->color ?? '#2563EB') }}' }">
        <label class="block text-sm font-medium mb-1">Boja</label>
        <div class="flex items-center gap-2">
            <input type="color" x-model="color" class="w-12 h-10 border border-app-border rounded-lg cursor-pointer">
            <input type="text" name="color" x-model="color" required pattern="^#[0-9A-Fa-f]{6}$"
                   class="flex-1 px-3 py-2 border border-app-border rounded-lg focus:outline-none focus:border-app-accent font-mono">
        </div>
        @error('color') <p class="mt-1 text-xs text-app-negative">{{ $message }}</p> @enderror
    </div>

    @php
        $icons = [
            'bi-tag', 'bi-cart', 'bi-house', 'bi-receipt', 'bi-car-front', 'bi-controller',
            'bi-cash-stack', 'bi-briefcase', 'bi-three-dots', 'bi-bag', 'bi-bank',
            'bi-piggy-bank', 'bi-wallet', 'bi-credit-card', 'bi-cup-hot', 'bi-airplane',
        ];
    @endphp
    <div x-data="{ icon: '{{ old('icon', $category->icon ?? 'bi-tag') }}' }">
        <label class="block text-sm font-medium mb-1">Ikonica</label>
        <input type="hidden" name="icon" x-model="icon">
        <div class="grid grid-cols-8 gap-2">
            @foreach ($icons as $i)
                <button type="button" @click="icon = '{{ $i }}'"
                        :class="icon === '{{ $i }}' ? 'border-app-accent bg-app-bg-soft' : 'border-app-border'"
                        class="w-10 h-10 border rounded-lg flex items-center justify-center hover:bg-app-bg-soft text-app-text">
                    <i class="bi {{ $i }}"></i>
                </button>
            @endforeach
        </div>
        @error('icon') <p class="mt-1 text-xs text-app-negative">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center justify-end gap-2 pt-2">
        <a href="{{ route('categories.index') }}" class="px-4 py-2 text-sm border border-app-border rounded-lg hover:bg-app-bg-soft">Otkazi</a>
        <button type="submit" class="px-4 py-2 text-sm bg-app-accent hover:bg-app-accent-hov text-white rounded-lg font-medium">Sacuvaj</button>
    </div>
</form>
