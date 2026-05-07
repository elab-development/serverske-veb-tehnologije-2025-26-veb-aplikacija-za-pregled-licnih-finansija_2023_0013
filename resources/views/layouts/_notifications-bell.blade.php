@php
    $unread = auth()->user()->unreadNotifications()->latest()->take(10)->get();
    $unreadCount = $unread->count();
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

    <div x-show="open" @click.outside="open = false" x-cloak
         class="absolute right-0 mt-2 w-80 bg-white border border-app-border rounded-xl shadow-lg z-40 notifications-dropdown">
        <div class="px-4 py-3 border-b border-app-border flex items-center justify-between">
            <h3 class="font-semibold text-sm">Notifikacije</h3>
            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="text-xs text-app-accent hover:underline mark-all-read">Oznaci sve</button>
                </form>
            @endif
        </div>
        <div class="max-h-80 overflow-y-auto">
            @if ($unread->isEmpty())
                <div class="px-4 py-6 text-center text-sm text-app-text-muted">Nema novih notifikacija.</div>
            @else
                @foreach ($unread as $notification)
                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="notification-form">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-3 border-b border-app-border last:border-b-0 hover:bg-app-bg-soft notification-item">
                            <div class="flex items-start gap-2">
                                <i class="bi bi-exclamation-triangle text-app-warning mt-0.5"></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm">{{ $notification->data['message'] ?? 'Upozorenje o budzetu.' }}</p>
                                    <p class="text-xs text-app-text-muted mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </button>
                    </form>
                @endforeach
            @endif
        </div>
    </div>
</div>
