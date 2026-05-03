<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold">Kategorije</h1>
        <a href="{{ route('categories.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-app-accent hover:bg-app-accent-hov text-white rounded-lg text-sm font-medium">
            <i class="bi bi-plus-lg"></i> Nova kategorija
        </a>
    </x-slot>

    <div class="space-y-6">
        @php
            $all = $income->concat($expense);
        @endphp

        @if ($all->isEmpty())
            <div class="bg-white border border-app-border rounded-xl p-8 text-center text-app-text-muted">
                Jos uvek nemate kategorija. Kliknite na "Nova kategorija" da dodate prvu.
            </div>
        @else
            <div class="bg-white border border-app-border rounded-xl divide-y divide-app-border">
                @foreach ($all as $category)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center text-white" style="background-color: {{ $category->color }}">
                            <i class="bi {{ $category->icon ?? 'bi-tag' }}"></i>
                        </span>
                        <div class="flex-1">
                            <div class="font-medium">{{ $category->name }}</div>
                            <div class="text-xs text-app-text-muted">{{ $category->type === 'income' ? 'Prihod' : 'Rashod' }}</div>
                        </div>
                        <a href="{{ route('categories.edit', $category) }}" class="text-app-text-muted hover:text-app-text">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-app-text-muted hover:text-app-negative">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
