@php
    // navigation.blade.php
    // Requer: TailwindCSS e Alpine.js carregados na página.
@endphp

<nav x-data="{ open: false, openSection: null }" x-on:keydown.escape.window="open = false; openSection = null" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow">
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
                    {{-- Grupo: Principal --}}
                    <div class="flex items-center gap-1">
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
                    </div>

                    {{-- Grupo: Loja --}}
                    <div class="flex items-center gap-1">
                        

                        <x-nav-link :href="route('favoritos.index')" :active="request()->routeIs('favoritos.*')" class="block">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-heart class="w-5 h-5" />
                                <span>{{ __('Favoritos') }}</span>
                            </div>
                        </x-nav-link>

                        
                    </div>

                    {{-- Grupo: Admin/Updates --}}
                    <div class="flex items-center gap-1">
                        

                        <x-nav-link :href="route('galerias.web.create')" :active="request()->routeIs('galerias.web.create')" class="block">
                            <div class="flex items-center gap-2">
                                <x-heroicon-m-folder-plus class="w-5 h-5" />
                                <span>{{ __('Nova Galeria') }}</span>
                            </div>
                        </x-nav-link>
                    </div>
                </div>

                <!-- Search (desktop) -->
                <div class="hidden md:flex items-center ml-6 relative" x-data="galeriaSearch()">
                    <div class="relative">
                        <div class="relative">
                            <input
                                x-model="term"
                                @input="doSearch()"
                                @focus="open = true"
                                @keydown.escape="open = false"
                                @keydown.enter.prevent="gotoSearch()"
                                type="search"
                                placeholder="Buscar galerias..."
                                class="w-44 py-2 px-3 pr-10 rounded-md border border-gray-200 bg-white text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm"
                            />

                            <button @click="gotoSearch()" type="button" class="absolute right-1 top-1/2 -translate-y-1/2 p-1 text-gray-500 hover:text-gray-700" aria-label="Pesquisar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                            </button>
                        </div>

                        <!-- Results dropdown -->
                        <div x-show="open" x-cloak class="absolute mt-2 right-0 w-56 bg-white rounded-md shadow-lg z-50">
                            <template x-if="results.length">
                                <ul class="divide-y max-h-64 overflow-auto">
                                    <template x-for="item in results" :key="item.id">
                                        <li @mouseenter="setPreview(item.banner && item.banner.imagem)" @mouseleave="setPreview(null)" @click="goto(item.id)" class="cursor-pointer">
                                            <div class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50">
                                                <img x-show="item.banner && item.banner.imagem" :src="`${baseUrl}/${item.banner.imagem}`" class="w-16 h-10 object-cover rounded" />
                                                <div class="flex-1">
                                                    <div class="font-medium text-sm" x-text="item.nome"></div>
                                                    <div class="text-xs text-gray-500" x-text="item.categoria ? item.categoria.nome : ''"></div>
                                                </div>
                                            </div>
                                        </li>
                                    </template>
                                </ul>
                            </template>

                            <template x-if="!results.length">
                                <div class="px-3 py-2 text-sm text-gray-500">Nenhum resultado</div>
                            </template>

                            <div class="border-t px-2 py-1 text-sm text-center">
                                <button @click.stop="gotoSearch()" class="w-full text-xs text-indigo-600 hover:underline">Ver todos os resultados</button>
                            </div>
                        </div>

                        <!-- Preview panel -->
                        <div x-show="previewUrl" x-cloak class="absolute -right-48 top-0 w-44 p-1 bg-white rounded-md shadow-md">
                            <img :src="previewUrl" class="w-full h-28 object-cover rounded" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Desktop user dropdown -->
            <div class="hidden sm:flex flex-wrap sm:items-center sm:space-x-4">
                <x-nav-link :href="route('carrinho.index')" :active="request()->routeIs('carrinho.*')" class="block py-1">
                    <div class="relative flex items-center gap-2">
                        <x-heroicon-s-shopping-cart class="w-4 h-4" />
                        

                        <span
                            id="cart-count-mobile"
                            class="absolute -top-1 left-3 min-w-[16px] h-[16px]
                                px-1 text-[10px] font-bold text-white
                                bg-red-600 rounded-full
                                flex items-center justify-center
                                hidden">
                            0
                        </span>
                    </div>
                </x-nav-link>


    @auth
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button type="button" aria-haspopup="true" aria-expanded="false"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition">
                    <span class="truncate">{{ Auth::user()->name }}</span>
                    <svg class="ms-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </x-slot>

            <x-slot name="content">

                {{-- Admin link (only admins / hosts) --}}
                @if(Auth::user()->isAdmin())
                    <x-dropdown-link :href="route('admin.dashboard')">
                        🛠 Admin
                    </x-dropdown-link>
                    <x-nav-link :href="route('updates.index')" :active="request()->routeIs('updates.*')" class="block">
                            <div class="flex items-center gap-2">
                                <x-heroicon-s-archive-box class="w-5 h-5" />
                                <span>{{ __('Updates') }}</span>
                            </div>
                    </x-nav-link>
                @endif

                <x-dropdown-link :href="route('profile.edit')">
                    {{ __('Perfil') }}
                </x-dropdown-link>
                <x-dropdown-link :href="route('inventario.web.index')" :active="request()->routeIs('inventario.*')" class="block">
                    <div class="flex items-center gap-2">
                        <x-heroicon-s-archive-box class="w-5 h-5" />
                        <span>{{ __('Inventário') }}</span>
                    </div>
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
    @endauth



    @guest
    <div class="flex items-center gap-3">

        {{-- Login --}}
        <a href="{{ route('login') }}"
           class="inline-flex items-center justify-center px-4 py-2 rounded-md
                  text-sm font-semibold
                  text-gray-700 dark:text-gray-200
                  border border-gray-300 dark:border-gray-600
                  hover:bg-gray-100 dark:hover:bg-gray-700
                  transition-all duration-200
                  focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Entrar
        </a>

        {{-- Register --}}
        @if (Route::has('register'))
            <a href="{{ route('register') }}"
               class="inline-flex items-center justify-center px-5 py-2 rounded-md
                      text-sm font-semibold
                      text-white
                      bg-indigo-600 hover:bg-indigo-700
                      shadow-sm hover:shadow-md
                      transition-all duration-200
                      focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Criar conta
            </a>
        @endif

    </div>
@endguest


</div>


            <!-- Mobile hamburger -->
            <div class="sm:hidden">
                <button
                    @click="open = !open; if (!open) openSection = null"
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

    <!-- Mobile menu panel with collapsible sections -->
    <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="sm:hidden border-t border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800" id="mobile-menu"
         @click.away="open = false; openSection = null"
    >
        <div class="px-4 pt-4 pb-3 space-y-2">
            <!-- Mobile search -->
            <div class="mb-2" x-data="galeriaSearch()">
                <input x-model="term" @input.debounce.300ms="doSearch()" @focus="open = true" @keydown.enter.prevent="gotoSearch()" type="search" placeholder="Buscar galerias..." class="w-full py-2 px-3 rounded-md border border-gray-200 bg-white text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm" />

                <div class="flex justify-end mt-2">
                    <button @click="gotoSearch()" x-show="term && term.length >= 2" type="button" class="px-3 py-1 text-sm text-indigo-600 hover:underline">Ver todos os resultados</button>
                </div>

                <div x-show="open" x-cloak class="mt-1 bg-white rounded-md shadow">
                    <template x-if="results.length">
                        <ul class="divide-y">
                            <template x-for="item in results" :key="item.id">
                                <li @click="goto(item.id)" class="px-3 py-2 hover:bg-gray-50 flex items-center gap-3">
                                    <img x-show="item.banner && item.banner.imagem" :src="`${baseUrl}/${item.banner.imagem}`" class="w-12 h-8 object-cover rounded" />
                                    <div>
                                        <div class="text-sm font-medium" x-text="item.nome"></div>
                                        <div class="text-xs text-gray-500" x-text="item.categoria ? item.categoria.nome : ''"></div>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </template>
                    <template x-if="!results.length">
                        <div class="px-3 py-2 text-sm text-gray-500">Nenhum resultado</div>
                    </template>
                </div>
            </div>
            {{-- Seção: Principal --}}
            <div class="border-b border-gray-100 dark:border-gray-700">
                <button
                    @click="openSection = (openSection === 'principal' ? null : 'principal')"
                    class="w-full flex items-center justify-between px-3 py-2 text-left hover:bg-gray-50 dark:hover:bg-gray-900 rounded-md"
                >
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-home class="w-5 h-5" />
                        <span class="font-medium">{{ __('Principal') }}</span>
                    </div>
                    <svg :class="{ 'transform rotate-180': openSection === 'principal' }" class="h-5 w-5 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="openSection === 'principal'" x-collapse class="px-3 pb-3">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')" class="block py-1">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-home class="w-4 h-4" />
                            <span>{{ __('Home') }}</span>
                        </div>
                    </x-nav-link>

                    <x-nav-link :href="route('galerias.web.index')" :active="request()->routeIs('galerias.web.*')" class="block py-1">
                        <div class="flex items-center gap-2">
                            <x-heroicon-s-photo class="w-4 h-4" />
                            <span>{{ __('Galerias') }}</span>
                        </div>
                    </x-nav-link>

                    <x-nav-link :href="route('categorias.web.index')" :active="request()->routeIs('categorias.*')" class="block py-1">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-square-3-stack-3d class="w-4 h-4" />
                            <span>{{ __('Categorias') }}</span>
                        </div>
                    </x-nav-link>
                </div>
            </div>

            {{-- Seção: Loja --}}
            <div class="border-b border-gray-100 dark:border-gray-700">
                <button
                    @click="openSection = (openSection === 'loja' ? null : 'loja')"
                    class="w-full flex items-center justify-between px-3 py-2 text-left hover:bg-gray-50 dark:hover:bg-gray-900 rounded-md"
                >
                    <div class="flex items-center gap-2">
                        <x-heroicon-s-shopping-cart class="w-5 h-5" />
                        <span class="font-medium">{{ __('Loja') }}</span>
                    </div>
                    <svg :class="{ 'transform rotate-180': openSection === 'loja' }" class="h-5 w-5 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="openSection === 'loja'" x-collapse class="px-3 pb-3">
                    <x-nav-link :href="route('carrinho.index')" :active="request()->routeIs('carrinho.*')" class="block py-1">
                        <div class="flex items-center gap-2">
                            <x-heroicon-s-shopping-cart class="w-4 h-4" />
                            <span>{{ __('Pedidos') }}</span>
                        </div>
                    </x-nav-link>

                    <x-nav-link :href="route('favoritos.index')" :active="request()->routeIs('favoritos.*')" class="block py-1">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-heart class="w-4 h-4" />
                            <span>{{ __('Favoritos') }}</span>
                        </div>
                    </x-nav-link>

                    <x-nav-link :href="route('inventario.web.index')" :active="request()->routeIs('inventario.*')" class="block py-1">
                        <div class="flex items-center gap-2">
                            <x-heroicon-s-archive-box class="w-4 h-4" />
                            <span>{{ __('Inventário') }}</span>
                        </div>
                    </x-nav-link>

                    <x-nav-link :href="route('galerias.web.create')" :active="request()->routeIs('galerias.web.create')" class="block py-1">
                        <div class="flex items-center gap-2">
                            <x-heroicon-m-folder-plus class="w-4 h-4" />
                            <span>{{ __('Nova Galeria') }}</span>
                        </div>
                    </x-nav-link>
                </div>
            </div>

            {{-- Seção: Admin / Updates --}}
            <div class="border-b border-gray-100 dark:border-gray-700">
                <button
                    @click="openSection = (openSection === 'admin' ? null : 'admin')"
                    class="w-full flex items-center justify-between px-3 py-2 text-left hover:bg-gray-50 dark:hover:bg-gray-900 rounded-md"
                >
                    <div class="flex items-center gap-2">
                        <x-heroicon-s-archive-box class="w-5 h-5" />
                        <span class="font-medium">{{ __('Admin / Updates') }}</span>
                    </div>
                    <svg :class="{ 'transform rotate-180': openSection === 'admin' }" class="h-5 w-5 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="openSection === 'admin'" x-collapse class="px-3 pb-3">
                    <x-nav-link :href="route('updates.index')" :active="request()->routeIs('updates.*')" class="block py-1">
                        <div class="flex items-center gap-2">
                            <x-heroicon-s-archive-box class="w-4 h-4" />
                            <span>{{ __('Updates') }}</span>
                        </div>
                    </x-nav-link>
                </div>
            </div>

            {{-- Divider --}}
            <div class="my-2 border-t border-gray-100 dark:border-gray-700"></div>

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

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('galeriaSearch', ()=>({
        term: '',
        results: [],
        open: false,
        previewUrl: null,
        timer: null,
        baseUrl: '{{ url('/') }}',

        doSearch() {
            clearTimeout(this.timer);
            if (!this.term || this.term.length < 2) {
                this.results = [];
                this.open = false;
                return;
            }

            this.timer = setTimeout(async () => {
                try {
                    const res = await fetch(`${this.baseUrl}/api/galerias?search=${encodeURIComponent(this.term)}&per_page=6`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!res.ok) { this.results = []; this.open = false; return; }
                    const json = await res.json();
                    this.results = json.data || [];
                    this.open = this.results.length > 0;
                } catch (e) {
                    this.results = [];
                    this.open = false;
                    console.error('Search error', e);
                }
            }, 300);
        },

        goto(id) {
            window.location.href = `${this.baseUrl}/galerias/${id}`;
        },

        gotoSearch() {
            if (!this.term || this.term.length < 1) return;
            const q = encodeURIComponent(this.term);
            window.location.href = `${this.baseUrl}/galerias/search?q=${q}`;
        },

        setPreview(path) {
            this.previewUrl = path ? `${this.baseUrl}/${path}` : null;
        }
    }));
});
</script>

@auth
<script>
document.addEventListener('DOMContentLoaded', () => {
    fetch('{{ route('carrinhos.show', auth()->id()) }}', {
        method: 'GET',
        credentials: 'same-origin', // ⭐ ESSENCIAL
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(res => res.ok ? res.json() : null)
    .then(data => {
        if (!data || !data.count) return;

        const badges = [
            document.getElementById('cart-count'),
            document.getElementById('cart-count-mobile')
        ];

        badges.forEach(badge => {
            if (!badge) return;
            badge.textContent = data.count;
            badge.classList.remove('hidden');
        });
    })
    .catch(err => console.error(err));
});
</script>
@endauth


