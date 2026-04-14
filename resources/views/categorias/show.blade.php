@extends('layouts.app')

@section('content')
@php
    $heroImage = $categoria->banner?->imagem ?? $categoria->thumbnail;
    $totalGalerias = $categoria->galerias->count();
@endphp

<div class="space-y-8">
    <section class="app-shell overflow-hidden">
        <div class="relative min-h-[20rem] px-6 py-8 sm:px-8">
            @if($heroImage)
                <img
                    src="{{ asset($heroImage) }}"
                    alt="{{ $categoria->nome }}"
                    class="absolute inset-0 h-full w-full object-cover opacity-25"
                >
            @endif

            <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(15,23,42,0.88),rgba(8,145,178,0.65),rgba(245,158,11,0.28))] dark:bg-[linear-gradient(135deg,rgba(2,6,23,0.92),rgba(8,47,73,0.8),rgba(120,53,15,0.28))]"></div>

            <div class="relative flex h-full flex-col justify-end gap-6">
                <div class="max-w-3xl space-y-3 text-white">
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-white/70">Categoria</p>
                    <h1 class="text-4xl font-semibold tracking-tight sm:text-5xl">{{ $categoria->nome }}</h1>
                    @if($categoria->descricao)
                        <p class="max-w-2xl text-base leading-7 text-white/80 sm:text-lg">{{ $categoria->descricao }}</p>
                    @endif
                </div>

                <div class="flex flex-wrap gap-3">
                    <div class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-medium text-white backdrop-blur">
                        {{ $totalGalerias }} {{ \Illuminate\Support\Str::plural('galeria', $totalGalerias) }}
                    </div>
                    <a href="{{ route('categorias.web.index') }}" class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-medium text-white backdrop-blur transition hover:bg-white/20">
                        Ver todas as categorias
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="app-panel p-6 sm:p-8">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="page-eyebrow">Colecoes</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-stone-950 dark:text-stone-50">Galerias em {{ $categoria->nome }}</h2>
            </div>
            <div class="stat-pill">
                <span>{{ $totalGalerias }}</span>
                <span class="text-stone-500 dark:text-stone-400">resultados</span>
            </div>
        </div>

        @if($categoria->galerias->isNotEmpty())
            <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @foreach($categoria->galerias as $galeria)
                    <a href="{{ route('galerias.web.show', $galeria->id) }}"
                       class="group overflow-hidden rounded-[1.75rem] border border-stone-200 bg-white shadow-[0_18px_38px_-30px_rgba(28,25,23,0.28)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_25px_50px_-32px_rgba(28,25,23,0.34)] dark:border-stone-800 dark:bg-stone-900">
                        @if($galeria->banner?->imagem)
                            <img
                                src="{{ asset($galeria->banner->imagem) }}"
                                alt="{{ $galeria->nome }}"
                                class="h-44 w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            >
                        @else
                            <div class="flex h-44 items-center justify-center bg-stone-100 text-sm text-stone-500 dark:bg-stone-800 dark:text-stone-400">
                                Sem imagem
                            </div>
                        @endif

                        <div class="space-y-3 p-5">
                            <h3 class="text-lg font-semibold text-stone-900 dark:text-stone-100">{{ $galeria->nome ?? $galeria->titulo }}</h3>
                            @if($galeria->descricao)
                                <p class="text-sm leading-6 text-stone-600 dark:text-stone-300">{{ Str::limit($galeria->descricao, 80) }}</p>
                            @else
                                <p class="text-sm text-stone-400 dark:text-stone-500">Galeria publicada nesta categoria.</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <p class="page-eyebrow">Sem galerias</p>
                <h3 class="mt-3 text-2xl font-semibold text-stone-950 dark:text-stone-50">Nenhuma galeria encontrada nesta categoria.</h3>
                <p class="mx-auto mt-3 max-w-2xl page-copy">Assim que uma coleção for publicada com essa categoria, ela aparece aqui.</p>
            </div>
        @endif
    </section>
</div>
@endsection
