@php
    $toasts = [];
    if (session('success')) $toasts[] = ['type' => 'success', 'message' => session('success')];
    if (session('error'))   $toasts[] = ['type' => 'error', 'message' => session('error')];
@endphp

@if (! empty($toasts))
    <div class="fixed top-4 right-4 z-50 space-y-2 toast-stack" x-data="{}" x-init="
        setTimeout(() => {
            $el.querySelectorAll('.toast').forEach(t => t.classList.add('opacity-0', 'translate-x-2'));
            setTimeout(() => $el.remove(), 400);
        }, 4000);
    ">
        @foreach ($toasts as $toast)
            <div class="toast toast-{{ $toast['type'] }} flex items-start gap-3 px-4 py-3 rounded-lg shadow-lg border min-w-72 max-w-md transition-all duration-300
                {{ $toast['type'] === 'success' ? 'bg-green-50 border-green-200 text-green-900' : 'bg-red-50 border-red-200 text-red-900' }}">
                <i class="bi {{ $toast['type'] === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle' }} text-lg"></i>
                <div class="text-sm flex-1">{{ $toast['message'] }}</div>
                <button type="button" @click="$el.closest('.toast').remove()" class="text-app-text-muted hover:text-app-text">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        @endforeach
    </div>
@endif
