<nav class="flex-1 p-3 space-y-1 text-sm">

    {{-- Dashboard --}}
    <a href="{{ route('admin.dashboard') }}"
       class="group flex items-center gap-3 px-3 py-2 rounded-md transition
              {{ request()->routeIs('admin.dashboard')
                    ? 'bg-gray-800 text-white'
                    : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
        <svg class="w-5 h-5 text-gray-400 group-hover:text-white"
             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M3 12h7V3H3v9zm11 9h7v-7h-7v7zM14 3v7h7V3h-7zM3 21h7v-7H3v7z"/>
        </svg>
        <span>Dashboard</span>
    </a>

    {{-- Users --}}
    <a href="{{ route('admin.users.index') }}"
       class="group flex items-center gap-3 px-3 py-2 rounded-md transition
              {{ request()->routeIs('admin.users.*')
                    ? 'bg-gray-800 text-white'
                    : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
        <svg class="w-5 h-5 text-gray-400 group-hover:text-white"
             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 00-3-3.87"/>
            <path d="M16 3.13a4 4 0 010 7.75"/>
        </svg>
        <span>Users</span>
    </a>

    {{-- Galerias --}}
    <a href="{{ route('admin.galerias.index') }}"
       class="group flex items-center gap-3 px-3 py-2 rounded-md transition
              {{ request()->routeIs('admin.galerias.*')
                    ? 'bg-gray-800 text-white'
                    : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
        <svg class="w-5 h-5 text-gray-400 group-hover:text-white"
             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="3" y="3" width="18" height="18" rx="2"/>
            <circle cx="8.5" cy="8.5" r="1.5"/>
            <path d="M21 15l-5-5L5 21"/>
        </svg>
        <span>Galerias</span>
    </a>

    {{-- Pedidos --}}
    <a href="{{ route('admin.pedidos.index') }}"
       class="group flex items-center gap-3 px-3 py-2 rounded-md transition
              {{ request()->routeIs('admin.pedidos.*')
                    ? 'bg-gray-800 text-white'
                    : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
        <svg class="w-5 h-5 text-gray-400 group-hover:text-white"
             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M9 17v-6h13v6"/>
            <path d="M9 5h13v6H9z"/>
            <path d="M3 7h6v10H3z"/>
        </svg>
        <span>Pedidos</span>
    </a>

    {{-- Categorias --}}
    <a href="{{ route('admin.categorias.index') }}"
       class="group flex items-center gap-3 px-3 py-2 rounded-md transition
              {{ request()->routeIs('admin.categorias.*')
                    ? 'bg-gray-800 text-white'
                    : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
        <svg class="w-5 h-5 text-gray-400 group-hover:text-white"
             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M3 7h6l2 3h10v9H3z"/>
        </svg>
        <span>Categorias</span>
    </a>

    {{-- Image Settings --}}
    <a href="{{ route('admin.image-settings.index') }}"
       class="group flex items-center gap-3 px-3 py-2 rounded-md transition
              {{ request()->routeIs('admin.image-settings.*')
                    ? 'bg-gray-800 text-white'
                    : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
        <svg class="w-5 h-5 text-gray-400 group-hover:text-white"
             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="3"/>
            <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 01-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09a1.65 1.65 0 00-1-1.51 1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09a1.65 1.65 0 001.51-1 1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
        </svg>
        <span>Image Settings</span>
    </a>

    <hr class="border-gray-700 my-3">

    {{-- Back to site --}}
    <a href="{{ route('home') }}"
       class="group flex items-center gap-3 px-3 py-2 rounded-md transition
              text-gray-300 hover:bg-gray-800 hover:text-white">
        <svg class="w-5 h-5 text-gray-400 group-hover:text-white"
             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M3 12l9-9 9 9"/>
            <path d="M9 21V9h6v12"/>
        </svg>
        <span>Voltar ao site</span>
    </a>

</nav>
