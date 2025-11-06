@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-10">
    {{-- Category Header --}}
    <div class="relative rounded-2xl overflow-hidden mb-10 shadow">
        @if($categoria->banner)
            <img 
                src="{{ asset($categoria->banner->imagem) }}" 
                alt="{{ $categoria->nome }}" 
                class="w-full h-64 object-cover"
            >
        @else
            <div class="w-full h-64 bg-gray-200 flex items-center justify-center text-gray-500 text-lg">
                No Banner
            </div>
        @endif

        <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
            <h1 class="text-4xl font-bold text-white drop-shadow-lg">{{ $categoria->nome }}</h1>
        </div>
    </div>

    {{-- Category Description --}}
    @if($categoria->descricao)
        <p class="text-gray-700 text-center max-w-3xl mx-auto mb-12">{{ $categoria->descricao }}</p>
    @endif

    {{-- Galleries Grid --}}
    <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Galerias em {{ $categoria->nome }}</h2>

    @if($categoria->galerias->isNotEmpty())
        <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($categoria->galerias as $galeria)
                <a href="{{ route('galerias.show', $galeria->id) }}" 
                   class="block bg-white rounded-2xl shadow hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100">
                    @if($galeria->banner)
                        <img 
                            src="{{ asset($galeria->banner->imagem) }}" 
                            alt="{{ $galeria->titulo }}" 
                            class="w-full h-40 object-cover"
                        >
                    @else
                        <div class="w-full h-40 bg-gray-200 flex items-center justify-center text-gray-400">
                            No Image
                        </div>
                    @endif
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800">{{ $galeria->titulo }}</h3>
                        @if($galeria->descricao)
                            <p class="text-sm text-gray-500 mt-1">{{ Str::limit($galeria->descricao, 60) }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <p class="text-center text-gray-500 mt-8">Nenhuma galeria encontrada nesta categoria.</p>
    @endif
</div>
@endsection
