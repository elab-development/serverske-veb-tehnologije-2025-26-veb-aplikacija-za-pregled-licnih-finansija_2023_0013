<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Licne Finansije') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-app-bg text-app-text">
    <header class="border-b border-app-border bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-app-accent text-white flex items-center justify-center">
                    <i class="bi bi-wallet2"></i>
                </div>
                <span class="text-lg font-semibold">Licne finansije</span>
            </div>
            @if (Route::has('login'))
                <nav class="flex items-center gap-2 text-sm">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-4 py-2 bg-app-accent text-white rounded-lg font-medium hover:bg-app-accent-hov">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 border border-app-border rounded-lg hover:bg-app-bg-soft">Prijavi se</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-app-accent text-white rounded-lg font-medium hover:bg-app-accent-hov">Registruj se</a>
                        @endif
                    @endauth
                </nav>
            @endif
        </div>
    </header>

    <main>
        <section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 text-center">
            <h1 class="text-3xl sm:text-5xl font-bold mb-4 leading-tight">
                Preuzmi kontrolu nad svojim novcem.
            </h1>
            <p class="text-lg text-app-text-muted mb-8">
                Prati prihode, rashode i mesecne budzete na jednom mestu. Dobij upozorenja kada
                predjes limit i izvezi izvestaje u PDF ili Excel.
            </p>
            @guest
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-app-accent text-white rounded-lg font-medium hover:bg-app-accent-hov">
                    Zapocni besplatno <i class="bi bi-arrow-right"></i>
                </a>
            @endguest
        </section>

        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6 pb-24">
            <div class="bg-white border border-app-border rounded-xl p-6">
                <div class="w-10 h-10 rounded-lg bg-app-accent text-white flex items-center justify-center mb-3">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <h3 class="font-semibold mb-1">Prati transakcije</h3>
                <p class="text-sm text-app-text-muted">Unosi prihode i rashode po kategorijama, filtriraj i pretrazuj.</p>
            </div>
            <div class="bg-white border border-app-border rounded-xl p-6">
                <div class="w-10 h-10 rounded-lg bg-app-warning text-white flex items-center justify-center mb-3">
                    <i class="bi bi-piggy-bank"></i>
                </div>
                <h3 class="font-semibold mb-1">Budzeti sa upozorenjima</h3>
                <p class="text-sm text-app-text-muted">Postavi mesecne limite i dobij notifikacije na 80% i 100%.</p>
            </div>
            <div class="bg-white border border-app-border rounded-xl p-6">
                <div class="w-10 h-10 rounded-lg bg-app-positive text-white flex items-center justify-center mb-3">
                    <i class="bi bi-bar-chart"></i>
                </div>
                <h3 class="font-semibold mb-1">Izvestaji PDF i Excel</h3>
                <p class="text-sm text-app-text-muted">Grafici po kategorijama i kretanje bilansa, eksport jednim klikom.</p>
            </div>
        </section>
    </main>
</body>
</html>
