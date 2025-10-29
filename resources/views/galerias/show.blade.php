@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-4">{{ $galeria->nome }}</h1>

    @if($galeria->categoria)
        <p class="text-gray-600 mb-2">
            Categoria: <strong>{{ $galeria->categoria->nome }}</strong>
        </p>
    @endif

    @if($galeria->descricao)
        <p class="mb-6 text-gray-700">{{ $galeria->descricao }}</p>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @forelse($galeria->fotos as $foto)
            <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition">
                <a href="{{ asset($foto->caminho_foto) }}" target="_blank">
                    <img src="{{ asset($foto->caminho_thumb) }}" alt="Foto da galeria" class="w-full h-48 object-cover">
                </a>
            </div>
        @empty
            <p class="text-gray-500 col-span-full text-center">Nenhuma foto disponível nesta galeria.</p>
        @endforelse
    </div>
</div>
@endsection
