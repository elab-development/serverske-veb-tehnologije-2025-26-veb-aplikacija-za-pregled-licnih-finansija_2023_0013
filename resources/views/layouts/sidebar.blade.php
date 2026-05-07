<div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 bg-black/40 z-30 lg:hidden"></div>

<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-40 w-60 bg-white border-r border-app-border flex flex-col transform transition-transform lg:translate-x-0">

    <div class="h-16 flex items-center gap-2 px-6 border-b border-app-border">
        <div class="w-8 h-8 rounded-lg bg-app-accent text-white flex items-center justify-center">
            <i class="bi bi-wallet2"></i>
        </div>
        <span class="text-lg font-semibold text-app-text">Licne finansije</span>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-app-text hover:bg-app-bg-soft {{ request()->routeIs('dashboard') ? 'bg-app-bg-soft font-medium' : '' }}">
            <i class="bi bi-grid"></i><span>Dashboard</span>
        </a>
        <a href="{{ route('transactions.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-app-text hover:bg-app-bg-soft {{ request()->routeIs('transactions.*') ? 'bg-app-bg-soft font-medium' : '' }}">
            <i class="bi bi-arrow-left-right"></i><span>Transakcije</span>
        </a>
        <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-app-text hover:bg-app-bg-soft {{ request()->routeIs('categories.*') ? 'bg-app-bg-soft font-medium' : '' }}">
            <i class="bi bi-tags"></i><span>Kategorije</span>
        </a>
        <a href="{{ route('budgets.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-app-text hover:bg-app-bg-soft {{ request()->routeIs('budgets.*') ? 'bg-app-bg-soft font-medium' : '' }}">
            <i class="bi bi-piggy-bank"></i><span>Budzeti</span>
        </a>
        <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-app-text hover:bg-app-bg-soft {{ request()->routeIs('reports.*') ? 'bg-app-bg-soft font-medium' : '' }}">
            <i class="bi bi-bar-chart"></i><span>Izvestaji</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-app-text hover:bg-app-bg-soft">
            <i class="bi bi-gear"></i><span>Podesavanja</span>
        </a>
    </nav>

    <div class="p-3 border-t border-app-border">
        <div class="flex items-center gap-3 px-2 py-2">
            <div class="w-8 h-8 rounded-full bg-app-accent text-white flex items-center justify-center text-xs font-semibold">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-medium truncate">{{ Auth::user()->name ?? '' }}</div>
                <div class="text-xs text-app-text-muted truncate">{{ Auth::user()->email ?? '' }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-app-text hover:bg-app-bg-soft text-sm">
                <i class="bi bi-box-arrow-right"></i><span>Odjavi se</span>
            </button>
        </form>
    </div>
</aside>

<style>[x-cloak]{display:none!important}</style>
