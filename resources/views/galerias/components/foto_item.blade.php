@props(['foto', 'index'])

@php
    $thumb = asset($foto->caminho_thumb ?? $foto->caminho_foto ?? $foto->caminho_original);
    $full = asset($foto->caminho_foto ?? $foto->caminho_original ?? $foto->caminho_thumb);
    $favoritoId = $favoritosFotosMap[$foto->id] ?? null;
    $isFavorito = filled($favoritoId);
@endphp

<article class="group relative overflow-hidden rounded-[24px] border border-stone-200 bg-white shadow-[0_16px_35px_-26px_rgba(20,20,20,0.55)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_24px_48px_-24px_rgba(20,20,20,0.45)]">
    <div class="relative h-56 overflow-hidden bg-stone-100">
        <button type="button"
            class="foto-open-btn block h-full w-full p-0 text-left focus:outline-none"
            data-index="{{ $index }}"
            data-src="{{ $full }}"
            data-thumb="{{ $thumb }}"
            data-title="{{ $foto->titulo ?? '' }}"
            data-desc="{{ Str::limit($foto->descricao ?? '', 200) }}">
            <img src="{{ $thumb }}" alt="{{ $foto->titulo ?? 'Foto' }}" class="h-full w-full rounded-[24px] object-cover transition duration-500 group-hover:scale-105">
        </button>

        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/75 via-black/10 to-transparent"></div>

        <div class="absolute left-3 right-3 top-3 flex items-start justify-between gap-3">
            <span class="rounded-full bg-black/45 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-white backdrop-blur-sm">
                Foto #{{ $foto->id }}
            </span>

            <div class="pointer-events-auto opacity-100 transition duration-300 md:opacity-0 md:group-hover:opacity-100">
                @auth
                    @if($isFavorito)
                        <form method="POST" action="{{ route('favoritos.destroy', $favoritoId) }}">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-rose-200 bg-rose-500/95 text-white shadow-lg backdrop-blur transition duration-300 hover:scale-105 hover:bg-rose-600"
                                aria-label="Remover foto dos favoritos"
                                title="Remover dos favoritos"
                            >
                                <x-heroicon-s-heart class="h-5 w-5" />
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('favoritos.store') }}">
                            @csrf
                            <input type="hidden" name="referencia_tipo" value="foto">
                            <input type="hidden" name="referencia_id" value="{{ $foto->id }}">
                            <button
                                type="submit"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/60 bg-white/85 text-stone-700 shadow-lg backdrop-blur transition duration-300 hover:scale-105 hover:bg-white"
                                aria-label="Adicionar foto aos favoritos"
                                title="Adicionar aos favoritos"
                            >
                                <x-heroicon-o-heart class="h-5 w-5" />
                            </button>
                        </form>
                    @endif
                @else
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/60 bg-white/85 text-stone-700 shadow-lg backdrop-blur transition duration-300 hover:scale-105 hover:bg-white"
                        aria-label="Entrar para favoritar foto"
                        title="Entrar para favoritar"
                    >
                        <x-heroicon-o-heart class="h-5 w-5" />
                    </a>
                @endauth
            </div>
        </div>

        <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
            <h3 class="text-base font-semibold">{{ $foto->titulo ?? 'Foto' }}</h3>
            <p class="mt-1 text-xs text-white/80">{{ Str::limit($foto->descricao ?? 'Clique para visualizar esta foto em destaque.', 72) }}</p>
        </div>
    </div>

    <div class="space-y-4 p-4">
        <div class="flex items-center justify-between text-xs uppercase tracking-[0.18em] text-stone-500">
            <span>Visualizacao rapida</span>
            <a href="{{ $full }}" target="_blank" class="font-semibold text-stone-700 transition hover:text-stone-900">Abrir arquivo</a>
        </div>

        <div class="flex items-center gap-2">
            <button type="button"
                    class="foto-open-btn inline-flex items-center justify-center rounded-full bg-stone-900 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-white transition hover:bg-stone-700"
                    data-index="{{ $index }}"
                    data-src="{{ $full }}"
                    data-thumb="{{ $thumb }}"
                    data-title="{{ $foto->titulo ?? '' }}"
                    data-desc="{{ Str::limit($foto->descricao ?? '', 200) }}">
                Ver
            </button>

            @auth
                <form class="inline add-to-cart-form" method="POST" action="{{ route('carrinho.store') }}">
                    @csrf
                    <input type="hidden" name="foto_id" value="{{ $foto->id }}">
                    <input type="hidden" name="preco" value="{{ $foto->preco ?? 0 }}">
                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-white transition hover:bg-emerald-500">
                        Adicionar
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-white transition hover:bg-emerald-500">
                    Entrar
                </a>
            @endauth
        </div>
    </div>
</article>
