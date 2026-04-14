<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased dark:bg-gray-800 bg-gray-100 text-stone-900 dark:bg-gray-900 dark:text-stone-100">
        <div class="relative min-h-screen overflow-x-hidden">
            {{-- Navbar (crie o arquivo resources/views/layouts/navigation.blade.php depois) --}}
            @include('layouts.navigation')

            {{-- Header opcional --}}
            @isset($header)
                <header class="px-4 pt-6 sm:px-6 lg:px-8 dark:bg-gray-800">
                    <div class="app-shell mx-auto max-w-7xl px-6 py-6 sm:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            {{-- Conteúdo principal --}}
            <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                @yield('content')
            </main>
        </div>
    </body>
</html>
