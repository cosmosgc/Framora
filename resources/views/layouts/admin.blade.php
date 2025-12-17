<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Panel')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-900 text-gray-100 hidden md:flex flex-col">
        <div class="p-4 text-xl font-bold border-b border-gray-700">
            Admin
        </div>

        @include('admin.partials.menu')
    </aside>

    {{-- Main --}}
    <main class="flex-1 flex flex-col">
        {{-- Topbar --}}
        <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
            <h1 class="text-lg font-semibold">@yield('header', 'Dashboard')</h1>

            <div class="text-sm text-gray-600">
                {{ auth()->user()->name ?? '' }}
            </div>
        </header>

        {{-- Content --}}
        <section class="p-6">
            @yield('content')
        </section>
    </main>

</body>
</html>
