<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Licne Finansije') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js" defer></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-app-bg text-app-text">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen flex">
            @include('layouts.sidebar')

            <div class="flex-1 flex flex-col min-w-0 lg:ml-60">
                <header class="bg-white border-b border-app-border lg:hidden">
                    <div class="flex items-center justify-between px-4 h-14">
                        <button @click="sidebarOpen = true" class="text-app-text">
                            <i class="bi bi-list text-2xl"></i>
                        </button>
                        <span class="font-semibold">{{ config('app.name') }}</span>
                        <span class="w-6"></span>
                    </div>
                </header>

                @isset($header)
                    <header class="bg-white border-b border-app-border">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4 flex-1 justify-between">
                                {{ $header }}
                            </div>
                            @auth
                                @include('layouts._notifications-bell')
                            @endauth
                        </div>
                    </header>
                @endisset

                <main class="flex-1">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                        @include('layouts._flash')
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
