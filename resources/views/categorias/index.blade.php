@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-10">
    <h1 class="text-4xl font-bold text-gray-800 mb-10 text-center">Categorias</h1>

    @if($categorias->isNotEmpty())
        <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @foreach($categorias as $categoria)
                <a href="{{ route('categorias.show', $categoria->id) }}" 
                   class="group block bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                    {{-- Name above image --}}
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white text-center py-2 font-semibold text-lg">
                        {{ $categoria->nome }}
                    </div>

                    {{-- Image / thumbnail --}}
                    @if($categoria->thumbnail)
                        <img 
                            src="{{ asset($categoria->thumbnail) }}" 
                            alt="{{ $categoria->nome }}" 
                            class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300"
                        >
                    @else
                        <div class="w-full h-40 bg-gray-200 flex items-center justify-center text-gray-400">
                            No Image
                        </div>
                    @endif

                    {{-- Description --}}
                    <div class="p-4">
                        @if($categoria->descricao)
                            <p class="text-sm text-gray-600 text-center">{{ Str::limit($categoria->descricao, 70) }}</p>
                        @else
                            <p class="text-sm text-gray-400 text-center italic">Sem descrição</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <p class="text-center text-gray-500 mt-8">Nenhuma categoria encontrada.</p>
    @endif
</div>
@endsection
