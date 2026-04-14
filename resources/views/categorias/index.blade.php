@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <section class="app-shell px-6 py-8 sm:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl space-y-3">
                <p class="page-eyebrow">Categorias</p>
                <h1 class="page-title">Navegue por tema sem perder a linguagem visual do restante do site.</h1>
                <p class="page-copy">
                    Cada categoria funciona como uma porta de entrada para novas galerias, com uma apresentação mais limpa e reaproveitável.
                </p>
            </div>

            <div class="stat-pill">
                <span>{{ $categorias->count() }}</span>
                <span class="text-stone-500 dark:text-stone-400">categorias</span>
            </div>
        </div>
    </section>

    @if($categorias->isNotEmpty())
        <section class="app-panel p-6 sm:p-8">
            <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @foreach($categorias as $categoria)
                    <a href="{{ route('categoria.show', $categoria->id) }}"
                       class="group overflow-hidden rounded-[1.75rem] border border-stone-200 bg-white shadow-[0_18px_38px_-30px_rgba(28,25,23,0.28)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_25px_50px_-32px_rgba(28,25,23,0.34)] dark:border-stone-800 dark:bg-stone-900">
                        <div class="border-b border-stone-200 bg-[linear-gradient(135deg,#0f766e,#0891b2,#1f2937)] px-5 py-4 text-white dark:border-stone-800">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/70">Categoria</p>
                            <h2 class="mt-2 text-xl font-semibold">{{ $categoria->nome }}</h2>
                        </div>

                        @if($categoria->thumbnail)
                            <img
                                src="{{ asset($categoria->thumbnail) }}"
                                alt="{{ $categoria->nome }}"
                                class="h-44 w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            >
                        @else
                            <div class="flex h-44 items-center justify-center bg-stone-100 text-sm text-stone-500 dark:bg-stone-800 dark:text-stone-400">
                                Sem imagem
                            </div>
                        @endif

                        <div class="space-y-3 p-5">
                            @if($categoria->descricao)
                                <p class="text-sm leading-6 text-stone-600 dark:text-stone-300">{{ Str::limit($categoria->descricao, 90) }}</p>
                            @else
                                <p class="text-sm italic text-stone-400 dark:text-stone-500">Sem descricao</p>
                            @endif

                            <span class="inline-flex rounded-full bg-stone-100 px-3 py-1 text-xs font-medium uppercase tracking-[0.18em] text-stone-700 dark:bg-stone-800 dark:text-stone-300">
                                Ver categoria
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @else
        <section class="empty-state">
            <p class="page-eyebrow">Sem categorias</p>
            <h2 class="mt-3 text-2xl font-semibold text-stone-950 dark:text-stone-50">Nenhuma categoria encontrada.</h2>
            <p class="mx-auto mt-3 max-w-2xl page-copy">Quando novas categorias forem criadas, elas aparecem aqui para organizar as galerias.</p>
        </section>
    @endif
</div>
@endsection
