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
                    <a href="{{ url('/categoria/' . $categoria->id) }}" 
                    class="block bg-white rounded-2xl shadow hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 group transform hover:-translate-y-1">
                        
                        {{-- Category name --}}
                        <div class="p-4 pb-0 flex flex-col items-center text-center">
                            <h3 class="text-lg font-bold text-gray-800 group-hover:text-indigo-600 transition">
                                {{ $categoria->nome }}
                            </h3>
                            <div class="w-10 h-1 bg-indigo-500 rounded-full mt-1 mb-3"></div>
                        </div>

                        {{-- Image --}}
                        @if($categoria->thumbnail)
                            <img 
                                src="{{ asset($categoria->thumbnail) }}" 
                                alt="{{ $categoria->nome }}" 
                                class="w-full h-32 object-cover transform group-hover:scale-105 transition duration-300"
                            >
                        @else
                            <div class="w-full h-32 bg-gray-200 flex items-center justify-center text-gray-400">
                                No Image
                            </div>
                        @endif

                        {{-- Description --}}
                        @if($categoria->descricao)
                            <div class="p-4">
                                <p class="text-sm text-gray-600 text-center">
                                    {{ Str::limit($categoria->descricao, 60) }}
                                </p>
                            </div>
                        @endif
                    </a>
                @endforeach

            </div>
        </div>
    @endif
</div>
@endsection
