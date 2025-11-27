@php
    // Importing Lucide icons (if using shadcn/ui or lucide-react not available in Blade)
    // These are inline SVGs for portability.
@endphp

<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center space-x-8">
                <!-- Placeholder Brand -->
                <a href="{{ route('home') }}" class="text-xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    📸 {{ config('app.name', 'Laravel') }}
                </a>

                <!-- Navigation Links -->
                <div class="hidden sm:flex space-x-6 items-center">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        <div class="flex items-center gap-1.5">
                            <x-heroicon-o-home class="w-4 h-4" />
                            <span>{{ __('Home') }}</span>
                        </div>
                    </x-nav-link>

                    <x-nav-link :href="route('galerias.web.index')" :active="request()->routeIs('galerias.web.*')">
                        <div class="flex items-center gap-1.5">
                            <x-heroicon-s-photo class="w-4 h-4" />
                            <span>{{ __('Galerias') }}</span>
                        </div>
                    </x-nav-link>

                    <x-nav-link :href="route('categorias.index')" :active="request()->routeIs('categorias.*')">
                        <div class="flex items-center gap-1.5">
                            <x-heroicon-o-square-3-stack-3d class="w-4 h-4" />
                            <span>{{ __('Categorias') }}</span>
                        </div>
                    </x-nav-link>

                    <x-nav-link :href="route('pedidos.web.index')" :active="request()->routeIs('carrinho.*')">
                        <div class="flex items-center gap-1.5">
                            <x-heroicon-s-shopping-cart class="w-4 h-4" />
                            <span>{{ __('Pedidos') }}</span>
                        </div>
                    </x-nav-link>

                    <x-nav-link :href="route('favoritos.index')" :active="request()->routeIs('favoritos.*')">
                        <div class="flex items-center gap-1.5">
                            <x-heroicon-o-heart class="w-4 h-4" />
                            <span>{{ __('Favoritos') }}</span>
                        </div>
                    </x-nav-link>

                    <x-nav-link :href="route('inventario.web.index')" :active="request()->routeIs('inventario.*')">
                        <div class="flex items-center gap-1.5">
                            <x-heroicon-s-archive-box class="w-4 h-4" />
                            <span>{{ __('Inventário') }}</span>
                        </div>
                    </x-nav-link>
                    <x-nav-link :href="route('updates.index')" :active="request()->routeIs('updates.*')">
                        <div class="flex items-center gap-1.5">
                            <x-heroicon-s-archive-box class="w-4 h-4" />
                            <span>{{ __('Updates') }}</span>
                        </div>
                    </x-nav-link>

                    <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                        <div class="flex items-center gap-1.5">
                            <x-heroicon-s-user class="w-4 h-4" />
                            <span>{{ __('Perfil') }}</span>
                        </div>
                    </x-nav-link>

                    <x-nav-link :href="route('galerias.web.create')" :active="request()->routeIs('galerias.web.create')">
                        <div class="flex items-center gap-1.5">
                            <x-heroicon-m-folder-plus class="w-4 h-4" />
                            <span>{{ __('Nova Galeria') }}</span>
                        </div>
                    </x-nav-link>
                    <x-nav-link :href="route('carrinho.index')" :active="request()->routeIs('carrinho.*')">
                        <div class="flex items-center gap-1.5">
                            <x-heroicon-s-shopping-cart class="w-4 h-4" />
                            <span>{{ __('Carrinho') }}</span>
                        </div>
                    </x-nav-link>
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:space-x-4">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition">
                            <div>{{ optional(Auth::user())->name ?? 'Convidado' }}</div>
                            <svg class="ms-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Perfil') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Sair') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Hamburger -->
            <div class="sm:hidden">
                <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>
