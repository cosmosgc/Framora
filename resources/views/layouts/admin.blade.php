<!DOCTYPE html>
<html lang="en"
      x-data="{ sidebarOpen: false }"
      x-cloak>
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Panel')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex overflow-x-hidden">

    {{-- Overlay (mobile) --}}
    <div
        x-show="sidebarOpen"
        x-transition.opacity
        class="fixed inset-0 bg-black/50 z-40 lg:hidden"
        @click="sidebarOpen = false">
    </div>

    {{-- Sidebar --}}
    <aside
        class="fixed lg:static inset-y-0 left-0 z-50
               w-64 bg-gray-900 text-gray-100
               transform transition-transform duration-300 ease-in-out
               shadow-xl lg:shadow-none"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        {{-- Sidebar Header --}}
        <div class="h-16 px-4 flex items-center justify-between
                    border-b border-gray-800">
            <span class="text-lg font-semibold tracking-wide">
                Admin Panel
            </span>

            {{-- Close (mobile only) --}}
            <button
                class="lg:hidden w-9 h-9 rounded-md
                       flex items-center justify-center
                       text-gray-400 hover:text-white hover:bg-gray-800"
                @click="sidebarOpen = false"
                aria-label="Close sidebar">
                ✕
            </button>
        </div>

        {{-- Menu --}}
        <div class="flex-1 overflow-y-auto">
            @include('admin.partials.menu')
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-h-screen">

        {{-- Topbar --}}
        <header
            class="sticky top-0 z-30
                   bg-white/80 backdrop-blur
                   border-b border-gray-200
                   h-16 px-4 lg:px-6
                   flex items-center justify-between">

            <div class="flex items-center gap-3">
                {{-- Toggle sidebar (mobile + desktop) --}}
                <button
                    class="w-10 h-10 rounded-md
                           flex items-center justify-center
                           text-gray-600 hover:bg-gray-200"
                    @click="sidebarOpen = !sidebarOpen"
                    aria-label="Toggle sidebar">
                    ☰
                </button>

                <h1 class="text-base lg:text-lg font-semibold text-gray-800">
                    @yield('header', 'Dashboard')
                </h1>
            </div>

            {{-- User --}}
            <div class="flex items-center gap-3">
                <!-- <span class="text-sm text-gray-600 hidden sm:block truncate max-w-[160px]">
                    {{ auth()->user()->name ?? '' }}
                </span> -->

                <div class="w-9 h-9 rounded-full bg-gray-300
                            flex items-center justify-center text-sm font-bold">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 10)) }}
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 p-4 lg:p-6">
            @yield('content')
        </main>

    </div>

</body>
</html>
