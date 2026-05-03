<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold">Izmeni kategoriju</h1>
    </x-slot>

    <div class="max-w-md bg-white border border-app-border rounded-xl p-6">
        @include('categories._form', [
            'category' => $category,
            'action' => route('categories.update', $category),
            'method' => 'PUT',
        ])
    </div>
</x-app-layout>
