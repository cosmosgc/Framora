@php
    // navigation.blade.php
    // Requer: TailwindCSS e Alpine.js carregados na página.
@endphp

<nav x-data="{ open: false }" x-on:keydown.escape.window="open = false" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Left: Brand + Desktop Links -->
            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-bold text-gray-800 dark:text-gray-100">
                    <span aria-hidden="true">📸</span>
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                    <span>{{ config('app.name', 'Laravel') }}</span>
                </a>

                <!-- Desktop links -->
                <div class="hidden sm:flex sm:items-center sm:space-x-4">
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

                    <x-nav-link :href="route('categorias.web.index')" :active="request()->routeIs('categorias.*')">
                        <div class="flex items-center gap-1.5">
                            <x-heroicon-o-square-3-stack-3d class="w-4 h-4" />
                            <span>{{ __('Categorias') }}</span>
                        </div>
                    </x-nav-link>

                    <x-nav-link :href="route('carrinho.index')" :active="request()->routeIs('carrinho.*')">
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

                    <x-nav-link :href="route('inventario.web.index')" :active="request()->routeIs('inventario.*')" class="block">
                        <div class="flex items-center gap-2">
                            <x-heroicon-s-archive-box class="w-5 h-5" />
                            <span>{{ __('Inventário') }}</span>
                        </div>
                    </x-nav-link>

                    <x-nav-link :href="route('galerias.web.create')" :active="request()->routeIs('galerias.web.create')" class="block">
                        <div class="flex items-center gap-2">
                            <x-heroicon-m-folder-plus class="w-5 h-5" />
                            <span>{{ __('Nova Galeria') }}</span>
                        </div>
                    </x-nav-link>
                </div>
            </div>

            <!-- Right: Desktop user dropdown -->
            <div class="hidden sm:flex sm:items-center sm:space-x-4">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button type="button" aria-haspopup="true" aria-expanded="false"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition">
                            <span class="truncate">{{ optional(Auth::user())->name ?? 'Convidado' }}</span>
                            <svg class="ms-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
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

            <!-- Mobile hamburger -->
            <div class="sm:hidden">
                <button
                    @click="open = !open"
                    x-bind:aria-expanded="open"
                    aria-controls="mobile-menu"
                    type="button"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    <span class="sr-only" x-text="open ? 'Fechar menu' : 'Abrir menu'"></span>

                    <!-- Icon: hamburger / close -->
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="open"  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu panel -->
    <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="sm:hidden border-t border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800" id="mobile-menu"
         @click.away="open = false"
    >
        <div class="px-4 pt-4 pb-3 space-y-2">
            {{-- Mobile nav links (duplicate of desktop) --}}
            <x-nav-link :href="route('home')" :active="request()->routeIs('home')" class="block">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-home class="w-5 h-5" />
                    <span>{{ __('Home') }}</span>
                </div>
            </x-nav-link>

            <x-nav-link :href="route('galerias.web.index')" :active="request()->routeIs('galerias.web.*')" class="block">
                <div class="flex items-center gap-2">
                    <x-heroicon-s-photo class="w-5 h-5" />
                    <span>{{ __('Galerias') }}</span>
                </div>
            </x-nav-link>

            <x-nav-link :href="route('categorias.web.index')" :active="request()->routeIs('categorias.*')" class="block">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-square-3-stack-3d class="w-5 h-5" />
                    <span>{{ __('Categorias') }}</span>
                </div>
            </x-nav-link>

            <x-nav-link :href="route('carrinho.index')" :active="request()->routeIs('carrinho.*')" class="block">
                <div class="flex items-center gap-2">
                    <x-heroicon-s-shopping-cart class="w-5 h-5" />
                    <span>{{ __('Pedidos') }}</span>
                </div>
            </x-nav-link>

            <x-nav-link :href="route('favoritos.index')" :active="request()->routeIs('favoritos.*')" class="block">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-heart class="w-5 h-5" />
                    <span>{{ __('Favoritos') }}</span>
                </div>
            </x-nav-link>

            <x-nav-link :href="route('inventario.web.index')" :active="request()->routeIs('inventario.*')" class="block">
                <div class="flex items-center gap-2">
                    <x-heroicon-s-archive-box class="w-5 h-5" />
                    <span>{{ __('Inventário') }}</span>
                </div>
            </x-nav-link>

            <x-nav-link :href="route('updates.index')" :active="request()->routeIs('updates.*')" class="block">
                <div class="flex items-center gap-2">
                    <x-heroicon-s-archive-box class="w-5 h-5" />
                    <span>{{ __('Updates') }}</span>
                </div>
            </x-nav-link>

            
            <x-nav-link :href="route('galerias.web.create')" :active="request()->routeIs('galerias.web.create')" class="block">
                <div class="flex items-center gap-2">
                    <x-heroicon-m-folder-plus class="w-5 h-5" />
                    <span>{{ __('Nova Galeria') }}</span>
                </div>
            </x-nav-link>

            <x-nav-link :href="route('carrinho.index')" :active="request()->routeIs('carrinho.*')" class="block">
                <div class="flex items-center gap-2">
                    <x-heroicon-s-shopping-cart class="w-5 h-5" />
                    <span>{{ __('Carrinho') }}</span>
                </div>
            </x-nav-link>

            {{-- Divider --}}
            <div class="border-t border-gray-100 dark:border-gray-700 my-2"></div>

            {{-- Mobile auth area --}}
            @auth
                <div class="px-1">
                    <div class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ auth()->user()->email }}</div>

                    <x-nav-link :href="route('profile.edit')" class="block">
                        {{ __('Perfil') }}
                    </x-nav-link>

                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                            {{ __('Sair') }}
                        </button>
                    </form>
                </div>
            @else
                <div class="px-1 space-y-1">
                    <x-nav-link :href="route('login')" class="block">{{ __('Entrar') }}</x-nav-link>
                    <x-nav-link :href="route('register')" class="block">{{ __('Registrar') }}</x-nav-link>
                </div>
            @endauth
        </div>
    </div>
</nav>
