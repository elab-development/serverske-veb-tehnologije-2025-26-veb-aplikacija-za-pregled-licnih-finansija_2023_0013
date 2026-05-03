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

    <div>
        <label class="block text-sm font-medium mb-1">Boja</label>
        <input type="text" name="color" value="{{ old('color', $category->color ?? '#2563EB') }}" required
               placeholder="#2563EB"
               class="w-full px-3 py-2 border border-app-border rounded-lg focus:outline-none focus:border-app-accent">
        @error('color') <p class="mt-1 text-xs text-app-negative">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Ikonica</label>
        <input type="text" name="icon" value="{{ old('icon', $category->icon) }}"
               placeholder="bi-tag"
               class="w-full px-3 py-2 border border-app-border rounded-lg focus:outline-none focus:border-app-accent">
        @error('icon') <p class="mt-1 text-xs text-app-negative">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center justify-end gap-2 pt-2">
        <a href="{{ route('categories.index') }}" class="px-4 py-2 text-sm border border-app-border rounded-lg hover:bg-app-bg-soft">Otkazi</a>
        <button type="submit" class="px-4 py-2 text-sm bg-app-accent hover:bg-app-accent-hov text-white rounded-lg font-medium">Sacuvaj</button>
    </div>
</form>
