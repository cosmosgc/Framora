@props(['galeria'])

@php
    $banner = $galeria->banner?->imagem ? asset($galeria->banner->imagem) : null;
    $avatar = $galeria->user?->avatar
        ? asset($galeria->user->avatar)
        : null;
@endphp

<article class="group relative overflow-hidden rounded-[28px] border border-stone-200 bg-white shadow-[0_18px_45px_-28px_rgba(20,20,20,0.45)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_28px_60px_-30px_rgba(20,20,20,0.55)]">
    <div class="relative h-64 overflow-hidden">
        @if($banner)
            <img
                src="{{ $banner }}"
                alt="{{ $galeria->nome }}"
                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
            >
        @else
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-stone-200 via-stone-100 to-white text-sm font-medium uppercase tracking-[0.25em] text-stone-500">
                Sem banner
            </div>
        @endif

        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/15 to-transparent"></div>

        <div class="absolute left-4 right-4 top-4 flex items-start justify-between gap-3">
            <span class="rounded-full bg-white/85 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-stone-700 backdrop-blur">
                {{ $galeria->categoria?->nome ?? 'Sem categoria' }}
            </span>

            @auth
                <form method="POST" action="{{ route('favoritos.store') }}" class="relative z-10">
                    @csrf
                    <input type="hidden" name="referencia_tipo" value="galeria">
                    <input type="hidden" name="referencia_id" value="{{ $galeria->id }}">
                    <button
                        type="submit"
                        class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/60 bg-white/85 text-stone-700 shadow-lg backdrop-blur transition duration-300 hover:scale-105 hover:bg-white"
                        aria-label="Adicionar galeria aos favoritos"
                        title="Adicionar aos favoritos"
                    >
                        <x-heroicon-o-heart class="h-5 w-5" />
                    </button>
                </form>
            @else
                <a
                    href="{{ route('login') }}"
                    class="relative z-10 inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/60 bg-white/85 text-stone-700 shadow-lg backdrop-blur transition duration-300 hover:scale-105 hover:bg-white"
                    aria-label="Entrar para favoritar galeria"
                    title="Entrar para favoritar"
                >
                    <x-heroicon-o-heart class="h-5 w-5" />
                </a>
            @endauth
        </div>

        <div class="absolute bottom-0 left-0 right-0 p-5 text-white">
            <p class="text-[11px] uppercase tracking-[0.28em] text-white/70">Colecao</p>
            <h2 class="mt-2 text-2xl font-semibold leading-tight">{{ $galeria->nome }}</h2>
            <div class="mt-3 flex flex-wrap gap-2 text-xs text-white/85">
                <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 backdrop-blur">
                    {{ $galeria->local ?? 'Local a definir' }}
                </span>
                <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 backdrop-blur">
                    {{ $galeria->data ?? 'Data aberta' }}
                </span>
            </div>
        </div>

        <a href="{{ route('galerias.web.show', $galeria->id) }}" class="absolute inset-0" aria-label="Ver galeria {{ $galeria->nome }}"></a>
    </div>

    <div class="relative space-y-4 p-5">
        <p class="line-clamp-3 text-sm leading-6 text-stone-600">
            {{ Str::limit($galeria->descricao ?: 'Explore esta galeria e descubra os destaques visuais reunidos pelo autor.', 140) }}
        </p>

        <div class="flex items-center justify-between gap-3 border-t border-stone-100 pt-4">
            <div class="flex min-w-0 items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-stone-200 text-sm font-semibold text-stone-700">
                    @if($avatar)
                        <img src="{{ $avatar }}" alt="{{ $galeria->user->name ?? 'Autor' }}" class="h-full w-full object-cover">
                    @else
                        {{ strtoupper(substr($galeria->user->name ?? 'G', 0, 1)) }}
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-stone-900">{{ $galeria->user->name ?? 'Autor desconhecido' }}</p>
                    <p class="text-xs uppercase tracking-[0.2em] text-stone-500">Ver detalhes</p>
                </div>
            </div>

            <a href="{{ route('galerias.web.show', $galeria->id) }}" class="inline-flex items-center gap-2 rounded-full bg-stone-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-stone-700">
                Abrir
                <span aria-hidden="true">→</span>
            </a>
        </div>
    </div>
</article>
