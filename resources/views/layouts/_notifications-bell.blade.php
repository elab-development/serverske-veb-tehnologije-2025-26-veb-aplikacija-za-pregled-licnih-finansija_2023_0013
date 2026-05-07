@php
    $unreadCount = auth()->user()->unreadNotifications()->count();
@endphp
<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" type="button" class="relative p-2 rounded-lg hover:bg-app-bg-soft notifications-bell">
        <i class="bi bi-bell text-xl"></i>
        @if ($unreadCount > 0)
            <span class="absolute -top-0 -right-0 bg-app-negative text-white text-xs rounded-full w-5 h-5 flex items-center justify-center notifications-count">
                {{ $unreadCount }}
            </span>
        @endif
    </button>
</div>
