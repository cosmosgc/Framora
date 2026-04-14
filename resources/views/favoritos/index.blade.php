@extends('layouts.app')

@section('title', 'Meus Favoritos')

@section('content')
<div class="space-y-8">
    <section class="app-shell px-6 py-8 sm:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl space-y-3">
                <p class="page-eyebrow">Favoritos</p>
                <h1 class="page-title">Tudo o que você salvou fica reunido em um painel mais organizado.</h1>
                <p class="page-copy">
                    Veja galerias favoritas, fotos salvas e os itens que já fazem parte do seu inventário em uma estrutura visual compatível com o restante do site.
                </p>
            </div>

            <div class="flex flex-wrap gap-3 text-sm">
                <div class="stat-pill">
                    <span>{{ $fotoFavoritosCount }}</span>
                    <span class="text-stone-500 dark:text-stone-400">fotos</span>
                </div>
                <div class="stat-pill">
                    <span>{{ $galeriaFavoritosCount }}</span>
                    <span class="text-stone-500 dark:text-stone-400">galerias</span>
                </div>
                <div class="stat-pill">
                    <span>{{ $ownedFotoFavoritosCount }}</span>
                    <span class="text-stone-500 dark:text-stone-400">compradas</span>
                </div>
            </div>
        </div>
    </section>

    <div class="space-y-3">
        @if(session('success'))
            <div class="app-alert app-alert-success">{{ session('success') }}</div>
        @endif
        @if(session('info'))
            <div class="app-alert app-alert-info">{{ session('info') }}</div>
        @endif
        @if($errors->any())
            <div class="app-alert app-alert-error">{{ $errors->first() }}</div>
        @endif
    </div>

    @if($favoritos->isEmpty())
        <section class="empty-state">
            <p class="page-eyebrow">Colecao vazia</p>
            <h2 class="mt-3 text-2xl font-semibold text-stone-950 dark:text-stone-50">Nenhum favorito salvo ainda.</h2>
            <p class="mx-auto mt-3 max-w-2xl page-copy">Quando você favoritar uma foto ou galeria, ela vai aparecer aqui.</p>
            <a href="{{ route('galerias.web.index') }}" class="btn-primary mt-6">Explorar galerias</a>
        </section>
    @else
        @php
            $sections = [
                'Galerias favoritas' => [
                    'items' => $galeriaFavoritos,
                    'empty' => 'Nenhuma galeria favoritada.',
                ],
                'Fotos favoritas' => [
                    'items' => $fotoFavoritos,
                    'empty' => 'Nenhuma foto favoritada que ainda nao esteja no seu inventario.',
                ],
                'Fotos que voce ja possui' => [
                    'items' => $ownedFotoFavoritos,
                    'empty' => 'Nenhuma foto favoritada encontrada no seu inventario.',
                ],
            ];
        @endphp

        <div class="space-y-10">
            @foreach($sections as $sectionTitle => $section)
                <section class="app-panel p-6 sm:p-8">
                    <div class="mb-6 flex items-center justify-between gap-4">
                        <div>
                            <p class="page-eyebrow">{{ $loop->first ? 'Destaques' : 'Colecao' }}</p>
                            <h2 class="mt-2 text-2xl font-semibold tracking-tight text-stone-950 dark:text-stone-50">{{ $sectionTitle }}</h2>
                        </div>
                        <span class="stat-pill">
                            <span>{{ $section['items']->count() }}</span>
                            <span class="text-stone-500 dark:text-stone-400">itens</span>
                        </span>
                    </div>

                    @if($section['items']->isEmpty())
                        <div class="empty-state !px-6 !py-8">
                            <p class="page-copy">{{ $section['empty'] }}</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                            @foreach($section['items'] as $favorito)
                                @php
                                    $item = $favorito->item;
                                    $isFoto = $favorito->referencia_tipo === 'foto';
                                    $thumb = $isFoto
                                        ? ($item?->caminho_thumb ?? $item?->caminho_foto ?? $item?->caminho_original)
                                        : ($item?->banner?->imagem ?? $item?->fotos->first()?->caminho_thumb ?? $item?->fotos->first()?->caminho_foto ?? $item?->fotos->first()?->caminho_original);
                                    $destino = $isFoto && $item?->galeria_id
                                        ? route('fotos.web.show', ['id' => $item->galeria_id, 'foto' => $item->id])
                                        : ($item ? route('galerias.web.show', $item->id) : null);
                                @endphp

                                <article class="overflow-hidden rounded-[1.75rem] border border-stone-200 bg-white shadow-[0_18px_38px_-30px_rgba(28,25,23,0.28)] dark:border-stone-800 dark:bg-stone-900">
                                    <div class="aspect-[4/3] bg-stone-100 dark:bg-stone-800">
                                        @if($thumb)
                                            <img src="{{ asset($thumb) }}" alt="" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full items-center justify-center text-sm text-stone-500 dark:text-stone-400">Sem imagem disponivel</div>
                                        @endif
                                    </div>

                                    <div class="space-y-4 p-5">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <div class="flex flex-wrap gap-2">
                                                    <span class="inline-flex rounded-full bg-stone-100 px-2.5 py-1 text-xs font-medium uppercase tracking-wide text-stone-700 dark:bg-stone-800 dark:text-stone-300">
                                                        {{ $isFoto ? 'Foto' : 'Galeria' }}
                                                    </span>
                                                    @if($favorito->is_owned ?? false)
                                                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium uppercase tracking-wide text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-200">
                                                            No inventario
                                                        </span>
                                                    @endif
                                                </div>
                                                <h3 class="mt-3 text-lg font-semibold text-stone-900 dark:text-stone-100">
                                                    @if($item)
                                                        {{ $isFoto ? 'Foto #'.$item->id : ($item->nome ?? 'Galeria #'.$item->id) }}
                                                    @else
                                                        Item indisponivel
                                                    @endif
                                                </h3>
                                            </div>

                                            <form action="{{ route('favoritos.destroy', $favorito->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-danger">Remover</button>
                                            </form>
                                        </div>

                                        <div class="text-sm leading-6 text-stone-600 dark:text-stone-300">
                                            @if($item && $isFoto)
                                                <p>Galeria: {{ $item->galeria->nome ?? 'Sem galeria' }}</p>
                                            @elseif($item)
                                                <p>{{ $item->descricao ?: 'Galeria salva nos seus favoritos.' }}</p>
                                                @if($item->banner?->titulo)
                                                    <p class="mt-1 text-xs text-stone-500 dark:text-stone-400">Banner: {{ $item->banner->titulo }}</p>
                                                @endif
                                            @else
                                                <p>O conteudo original deste favorito nao esta mais disponivel.</p>
                                            @endif
                                            <p class="mt-2">Salvo em {{ optional($favorito->criado_em)->format('d/m/Y H:i') ?? 'data indisponivel' }}</p>
                                        </div>

                                        @if($destino)
                                            <a href="{{ $destino }}" class="btn-primary">
                                                {{ $isFoto ? 'Ver foto' : 'Ver galeria' }}
                                            </a>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endforeach
        </div>
    @endif
</div>
@endsection
