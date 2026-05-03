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
                <div class="bg-white border border-app-border rounded-xl">
                    @if ($section['items']->isEmpty())
                        <div class="p-6 text-center text-app-text-muted text-sm">{{ $section['empty'] }}</div>
                    @else
                        <div class="divide-y divide-app-border">
                            @foreach ($section['items'] as $category)
                                <div class="flex items-center gap-3 px-5 py-3 category-row">
                                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-white" style="background-color: {{ $category->color }}">
                                        <i class="bi {{ $category->icon ?? 'bi-tag' }}"></i>
                                    </span>
                                    <div class="flex-1 font-medium">{{ $category->name }}</div>
                                    <a href="{{ route('categories.edit', $category) }}" class="text-app-text-muted hover:text-app-text">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Obrisati kategoriju {{ $category->name }}?')">
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
            </div>
        @endforeach
    </div>
</x-app-layout>
