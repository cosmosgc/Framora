@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    {{-- Galerias Section --}}
    <h1 class="text-3xl font-bold mb-8 text-gray-800">Mais recentes</h1>

    @if($galerias->isEmpty())
        <p class="text-gray-600">Nenhuma galeria encontrada.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($galerias as $galeria)
                <x-galeria.galeria-card :galeria="$galeria" />
            @endforeach
        </div>
    @endif

    {{-- Categorias Section --}}
    @if($categorias->isNotEmpty())
        <div class="mt-16 border-t border-gray-200 pt-10">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Categorias</h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
                @foreach($categorias as $categoria)
                    <x-galeria.categoria-card :categoria="$categoria" />
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
