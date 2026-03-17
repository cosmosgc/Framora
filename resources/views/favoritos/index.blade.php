@extends('layouts.app')

@section('title', 'Meus Favoritos')

@section('content')
<div class="container mx-auto max-w-6xl p-4">
    <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Meus Favoritos</h1>
            <p class="text-sm text-gray-600">Veja suas galerias favoritas, fotos salvas e as fotos que voce ja possui no inventario.</p>
        </div>

        <div class="flex flex-wrap gap-3 text-sm">
            <div class="rounded-lg bg-white px-4 py-3 shadow-sm ring-1 ring-gray-200">
                <div class="text-gray-500">Fotos</div>
                <div class="text-lg font-semibold text-gray-900">{{ $fotoFavoritosCount }}</div>
            </div>
            <div class="rounded-lg bg-white px-4 py-3 shadow-sm ring-1 ring-gray-200">
                <div class="text-gray-500">Galerias</div>
                <div class="text-lg font-semibold text-gray-900">{{ $galeriaFavoritosCount }}</div>
            </div>
            <div class="rounded-lg bg-white px-4 py-3 shadow-sm ring-1 ring-gray-200">
                <div class="text-gray-500">Fotos compradas</div>
                <div class="text-lg font-semibold text-gray-900">{{ $ownedFotoFavoritosCount }}</div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="mb-4 rounded-lg bg-blue-100 px-4 py-3 text-blue-800">{{ session('info') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800">{{ $errors->first() }}</div>
    @endif

    @if($favoritos->isEmpty())
        <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center shadow-sm">
            <h2 class="text-lg font-medium text-gray-900">Nenhum favorito salvo ainda</h2>
            <p class="mt-2 text-sm text-gray-600">Quando você favoritar uma foto ou galeria, ela vai aparecer aqui.</p>
            <a href="{{ route('galerias.web.index') }}" class="mt-5 inline-flex rounded-lg bg-black px-4 py-2 text-sm font-medium text-white">
                Explorar galerias
            </a>
        </div>
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
                <section>
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900">{{ $sectionTitle }}</h2>
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium uppercase tracking-wide text-gray-700">
                            {{ $section['items']->count() }} itens
                        </span>
                    </div>

                    @if($section['items']->isEmpty())
                        <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-8 text-sm text-gray-600 shadow-sm">
                            {{ $section['empty'] }}
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

                                <article class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">
                                    <div class="aspect-[4/3] bg-gray-100">
                                        @if($thumb)
                                            <img src="{{ asset($thumb) }}" alt="" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full items-center justify-center text-sm text-gray-500">Sem imagem disponivel</div>
                                        @endif
                                    </div>

                                    <div class="space-y-3 p-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <div class="flex flex-wrap gap-2">
                                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium uppercase tracking-wide text-gray-700">
                                                        {{ $isFoto ? 'Foto' : 'Galeria' }}
                                                    </span>
                                                    @if($favorito->is_owned ?? false)
                                                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium uppercase tracking-wide text-emerald-700">
                                                            No inventario
                                                        </span>
                                                    @endif
                                                </div>
                                                <h3 class="mt-2 text-lg font-semibold text-gray-900">
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
                                                <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-50">
                                                    Remover
                                                </button>
                                            </form>
                                        </div>

                                        <div class="text-sm text-gray-600">
                                            @if($item && $isFoto)
                                                <p>Galeria: {{ $item->galeria->nome ?? 'Sem galeria' }}</p>
                                            @elseif($item)
                                                <p>{{ $item->descricao ?: 'Galeria salva nos seus favoritos.' }}</p>
                                                @if($item->banner?->titulo)
                                                    <p class="mt-1 text-xs text-gray-500">Banner: {{ $item->banner->titulo }}</p>
                                                @endif
                                            @else
                                                <p>O conteudo original deste favorito nao esta mais disponivel.</p>
                                            @endif
                                            <p class="mt-1">Salvo em {{ optional($favorito->criado_em)->format('d/m/Y H:i') ?? 'data indisponivel' }}</p>
                                        </div>

                                        @if($destino)
                                            <a href="{{ $destino }}" class="inline-flex rounded-lg bg-black px-4 py-2 text-sm font-medium text-white">
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
