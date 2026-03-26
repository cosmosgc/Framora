@extends('layouts.app')

@section('content')
@php
    $avatar = $user->avatar;
    $avatarUrl = $avatar
        ? (\Illuminate\Support\Str::startsWith($avatar, ['http://', 'https://'])
            ? $avatar
            : asset($avatar))
        : null;
    $coverUrl = $destaque?->banner?->imagem ? asset($destaque->banner->imagem) : null;
    $initial = strtoupper(substr($user->name ?? 'U', 0, 1));
@endphp

<div class="space-y-10">
    <section class="relative overflow-hidden rounded-[2rem] border border-stone-200 bg-stone-950 text-white shadow-[0_30px_80px_-40px_rgba(15,23,42,0.8)]">
        <div class="absolute inset-0">
            @if($coverUrl)
                <img src="{{ $coverUrl }}" alt="Capa do perfil de {{ $user->name }}" class="h-full w-full object-cover opacity-30">
            @endif
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(251,191,36,0.25),_transparent_32%),linear-gradient(135deg,rgba(12,10,9,0.9),rgba(28,25,23,0.78),rgba(120,53,15,0.82))]"></div>
        </div>

        <div class="relative grid gap-8 px-6 py-10 sm:px-10 lg:grid-cols-[minmax(0,1fr)_320px] lg:items-end lg:px-12">
            <div class="space-y-6">
                <span class="inline-flex w-fit items-center rounded-full border border-white/15 bg-white/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.32em] text-amber-100 backdrop-blur">
                    Perfil do artista
                </span>

                <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                    <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-full border-4 border-white/20 bg-white/15 text-3xl font-semibold text-white shadow-lg">
                        @if($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="Avatar de {{ $user->name }}" class="h-full w-full object-cover">
                        @else
                            {{ $initial }}
                        @endif
                    </div>

                    <div class="space-y-3">
                        <div>
                            <p class="text-sm uppercase tracking-[0.28em] text-white/65">Comunidade Framora</p>
                            <h1 class="mt-2 text-4xl font-semibold tracking-tight sm:text-5xl">{{ $user->name }}</h1>
                        </div>

                        <div class="flex flex-wrap gap-3 text-sm text-white/80">
                            <span class="rounded-full border border-white/15 bg-white/10 px-3 py-1 backdrop-blur">
                                Membro desde {{ optional($user->created_at)->format('M \d\e Y') ?? 'recentemente' }}
                            </span>
                            @if($stats['total_galerias'] > 0)
                                <span class="rounded-full border border-white/15 bg-white/10 px-3 py-1 backdrop-blur">
                                    {{ $stats['total_galerias'] }} galerias publicadas
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <p class="max-w-3xl text-base leading-7 text-white/78 sm:text-lg">
                    {{ $user->bio ?: 'Este criador ainda não adicionou uma bio, mas as galerias abaixo mostram bem o estilo e o olhar que ele traz para a comunidade.' }}
                </p>

                @if($categorias->isNotEmpty())
                    <div class="flex flex-wrap gap-3">
                        @foreach($categorias as $categoria)
                            <span class="rounded-full bg-white/10 px-4 py-2 text-sm font-medium text-white/85 ring-1 ring-inset ring-white/10 backdrop-blur">
                                {{ $categoria }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                <div class="rounded-[1.5rem] border border-white/10 bg-white/10 p-5 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.24em] text-white/55">Galerias</p>
                    <p class="mt-3 text-3xl font-semibold">{{ $stats['total_galerias'] }}</p>
                    <p class="mt-2 text-sm text-white/70">Colecoes publicadas neste perfil.</p>
                </div>

                <div class="rounded-[1.5rem] border border-white/10 bg-white/10 p-5 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.24em] text-white/55">Fotos</p>
                    <p class="mt-3 text-3xl font-semibold">{{ $stats['total_fotos'] }}</p>
                    <p class="mt-2 text-sm text-white/70">Imagens distribuidas nas galerias abertas.</p>
                </div>

                <div class="rounded-[1.5rem] border border-white/10 bg-white/10 p-5 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.24em] text-white/55">Categorias</p>
                    <p class="mt-3 text-3xl font-semibold">{{ $stats['total_categorias'] }}</p>
                    <p class="mt-2 text-sm text-white/70">Temas e colecoes explorados por {{ $user->name }}.</p>
                </div>

                <div class="rounded-[1.5rem] border border-white/10 bg-white/10 p-5 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.24em] text-white/55">Valor medio</p>
                    <p class="mt-3 text-3xl font-semibold">
                        @if(!is_null($stats['valor_medio']))
                            R$ {{ number_format($stats['valor_medio'], 2, ',', '.') }}
                        @else
                            --
                        @endif
                    </p>
                    <p class="mt-2 text-sm text-white/70">Media de preco por foto nas galerias com valor definido.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
        <div class="space-y-6">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-amber-700">Portifolio</p>
                    <h2 class="mt-2 text-3xl font-semibold tracking-tight text-stone-900">Galerias publicadas</h2>
                </div>
                <div class="rounded-full bg-stone-100 px-4 py-2 text-sm text-stone-600">
                    {{ $stats['total_galerias'] }} {{ \Illuminate\Support\Str::plural('resultado', $stats['total_galerias']) }}
                </div>
            </div>

            @if($galerias->isNotEmpty())
                <div class="grid gap-6 md:grid-cols-2">
                    @foreach($galerias as $galeria)
                        <x-galeria.galeria-card :galeria="$galeria" />
                    @endforeach
                </div>
            @else
                <div class="rounded-[2rem] border border-dashed border-stone-300 bg-stone-50 px-8 py-16 text-center">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-stone-500">Nada publicado ainda</p>
                    <h3 class="mt-3 text-2xl font-semibold text-stone-900">Este perfil ainda esta montando o portifolio.</h3>
                    <p class="mt-4 text-base leading-7 text-stone-600">
                        Quando novas galerias forem publicadas, elas vao aparecer aqui com capa, categoria e acesso rapido.
                    </p>
                </div>
            @endif
        </div>

        <aside class="space-y-5">
            <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-[0_20px_50px_-35px_rgba(28,25,23,0.45)]">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-amber-700">Resumo</p>
                <dl class="mt-5 space-y-4 text-sm text-stone-600">
                    <div class="flex items-center justify-between gap-4 border-b border-stone-100 pb-4">
                        <dt>Nome publico</dt>
                        <dd class="text-right font-semibold text-stone-900">{{ $user->name }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 border-b border-stone-100 pb-4">
                        <dt>Desde</dt>
                        <dd class="text-right font-semibold text-stone-900">{{ optional($user->created_at)->format('d/m/Y') ?? '--' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 border-b border-stone-100 pb-4">
                        <dt>Ultima galeria</dt>
                        <dd class="text-right font-semibold text-stone-900">{{ $destaque?->nome ?? '--' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt>Total de fotos</dt>
                        <dd class="text-right font-semibold text-stone-900">{{ $stats['total_fotos'] }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-[1.75rem] border border-amber-200 bg-gradient-to-br from-amber-50 via-white to-orange-50 p-6 shadow-[0_20px_50px_-35px_rgba(180,83,9,0.35)]">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-amber-700">Destaque</p>
                <h3 class="mt-3 text-2xl font-semibold text-stone-900">
                    {{ $destaque?->nome ?? 'Perfil em construcao' }}
                </h3>
                <p class="mt-3 text-sm leading-7 text-stone-700">
                    @if($destaque)
                        {{ \Illuminate\Support\Str::limit($destaque->descricao ?: 'A colecao mais recente deste perfil aparece aqui para guiar novos visitantes.', 150) }}
                    @else
                        Assim que a primeira galeria for publicada, esta area passa a destacar a colecao mais nova do perfil.
                    @endif
                </p>

                @if($destaque)
                    <a
                        href="{{ route('galerias.web.show', $destaque->id) }}"
                        class="mt-5 inline-flex items-center gap-2 rounded-full bg-stone-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-stone-700"
                    >
                        Abrir galeria
                        <span aria-hidden="true">→</span>
                    </a>
                @endif
            </div>
        </aside>
    </section>
</div>
@endsection
