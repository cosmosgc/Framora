@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <section class="app-shell px-6 py-8 sm:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl space-y-3">
                <p class="page-eyebrow">Inicio</p>
                <h1 class="page-title">Descubra as galerias mais recentes com uma navegação mais consistente.</h1>
                <p class="page-copy">
                    A página inicial agora segue a mesma estrutura visual do restante da experiência, com foco nas coleções e nas categorias.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <div class="stat-pill">
                    <span>{{ $galerias->count() }}</span>
                    <span class="text-stone-500 dark:text-stone-400">galerias</span>
                </div>
                <div class="stat-pill">
                    <span>{{ $categorias->count() }}</span>
                    <span class="text-stone-500 dark:text-stone-400">categorias</span>
                </div>
            </div>
        </div>
    </section>

    <section class="app-panel p-6 sm:p-8">
        <div class="mb-6">
            <p class="page-eyebrow">Mais recentes</p>
            <h2 class="mt-2 text-2xl font-semibold tracking-tight text-stone-950 dark:text-stone-50">Galerias em destaque</h2>
        </div>

        @if($galerias->isEmpty())
            <div class="empty-state">
                <h3 class="text-2xl font-semibold text-stone-950 dark:text-stone-50">Nenhuma galeria encontrada.</h3>
                <p class="mx-auto mt-3 max-w-2xl page-copy">Assim que novas publicações forem criadas, elas aparecem aqui.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @foreach($galerias as $galeria)
                    <x-galeria.galeria-card :galeria="$galeria" />
                @endforeach
            </div>
        @endif
    </section>

    @if($categorias->isNotEmpty())
        <section class="app-panel p-6 sm:p-8">
            <div class="mb-6">
                <p class="page-eyebrow">Organizacao</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-stone-950 dark:text-stone-50">Categorias</h2>
                <p class="mt-2 page-copy">Entre por tema para encontrar coleções com o mesmo clima visual.</p>
            </div>

            <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                @foreach($categorias as $categoria)
                    <x-galeria.categoria-card :categoria="$categoria" />
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
