<nav class="flex-1 p-4 space-y-2 text-sm">

    <a href="{{ route('admin.dashboard') }}"
       class="block px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800' : '' }}">
        📊 Dashboard
    </a>
    <a href="{{ route('admin.users.index') }}"
       class="block px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.users.*') ? 'bg-gray-800' : '' }}">
        🖼 Users
    </a>

    <a href="{{ route('admin.categorias.index') }}"
       class="block px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.categorias.*') ? 'bg-gray-800' : '' }}">
        🗂 Categorias
    </a>

    <a href="{{ route('admin.image-settings.index') }}"
       class="block px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.image-settings.*') ? 'bg-gray-800' : '' }}">
        🖼 Image Settings
    </a>

    <hr class="border-gray-700 my-3">

    <a href="{{ route('home') }}"
       class="block px-3 py-2 rounded hover:bg-gray-800">
        🌐 Voltar ao site
    </a>

</nav>
