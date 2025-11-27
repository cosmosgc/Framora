@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Seu Carrinho</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="bg-blue-100 text-blue-800 p-3 rounded mb-4">{{ session('info') }}</div>
    @endif

    @if($carrinho->fotos->isEmpty())
        <p>Seu carrinho está vazio.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($carrinho->fotos as $item)
                <div class="flex bg-white rounded-lg shadow p-4 items-center">
                    <img src="{{ $item->foto->caminho_thumb ?? $item->foto->caminho_foto ?? '#' }}" alt=""
                         class="w-24 h-24 object-cover rounded mr-4">
                    <div class="flex-1">
                        <h3 class="font-medium">foto: {{ $item->foto->id ?? 'sem identificação' }}</h3>
                        <p class="text-sm text-gray-600">Preço: R$ {{ number_format($item->foto->galeria->valor_foto, 2, ',', '.') }}</p>
                        <form action="{{ route('carrinho.destroy', $item->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="mt-2 inline-block px-3 py-1 bg-red-500 text-white rounded">Remover</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 p-4 bg-gray-50 rounded">
            <p class="text-lg font-semibold">
                Total:
                    R$ {{ number_format(
                            $carrinho->fotos->sum(fn($i) => $i->foto->galeria->valor_foto),
                            2, ',', '.'
                        ) }}
            </p>

            <form action="{{ route('carrinho.checkout') }}" method="POST" class="mt-4">
                @csrf
                <label class="block mb-2">Forma de pagamento</label>
                <select name="forma_pagamento" required class="border p-2 rounded w-full md:w-1/3">
                    <option value="pix">PIX</option>
                    <option value="cartao">Cartão</option>
                    <option value="boleto">Boleto</option>
                </select>

                <button type="submit" class="mt-4 px-4 py-2 bg-green-600 text-white rounded">Finalizar compra</button>
            </form>
        </div>
    @endif
</div>
@endsection
