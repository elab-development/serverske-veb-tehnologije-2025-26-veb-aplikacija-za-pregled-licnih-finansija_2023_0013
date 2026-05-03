<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold">Kategorije</h1>
        <a href="{{ route('categories.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-app-accent hover:bg-app-accent-hov text-white rounded-lg text-sm font-medium">
            <i class="bi bi-plus-lg"></i> Nova kategorija
        </a>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach ([['title' => 'Prihodi', 'items' => $income, 'empty' => 'Nemate prihodnih kategorija.'], ['title' => 'Rashodi', 'items' => $expense, 'empty' => 'Nemate rashodnih kategorija.']] as $section)
            <div>
                <h2 class="text-lg font-semibold mb-3">{{ $section['title'] }}</h2>
                @if ($section['items']->isEmpty())
                    <div class="bg-white border border-app-border rounded-xl p-6 text-center text-app-text-muted text-sm">{{ $section['empty'] }}</div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($section['items'] as $category)
                            <div class="bg-white border border-app-border rounded-xl p-4 flex items-center gap-3 category-row hover:shadow-sm">
                                <span class="w-10 h-10 rounded-lg flex items-center justify-center text-white text-lg" style="background-color: {{ $category->color }}">
                                    <i class="bi {{ $category->icon ?? 'bi-tag' }}"></i>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium truncate category-name">{{ $category->name }}</div>
                                    <div class="text-xs text-app-text-muted">{{ $category->transactions()->count() }} transakcija</div>
                                </div>
                                <a href="{{ route('categories.edit', $category) }}" class="text-app-text-muted hover:text-app-text" title="Izmeni">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline category-delete-form" onsubmit="return confirm('Da li ste sigurni da zelite da obrisete kategoriju &quot;{{ $category->name }}&quot;?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-app-text-muted hover:text-app-negative delete-category-btn" title="Obrisi">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</x-app-layout>
