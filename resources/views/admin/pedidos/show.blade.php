@extends('layouts.admin')

@section('title', 'Pedido #' . $pedido->id)
@section('header', 'Pedido #' . $pedido->id)

@section('content')
<div class="mb-3 text-sm">
    <strong>Origem dos itens:</strong>
    @if($source === 'inventario')
        <span class="text-blue-600">Inventário (pós-pagamento)</span>
    @else
        <span class="text-gray-600">Carrinho</span>
    @endif
</div>

<div class="mb-4 text-sm text-gray-700 space-y-1">
    <div><strong>Usuário:</strong> {{ $pedido->user->name ?? '—' }}</div>
    <div><strong>Status:</strong> {{ ucfirst($pedido->status_pedido) }}</div>
    <div><strong>Pagamento:</strong> {{ ucfirst($pedido->forma_pagamento) }}</div>
</div>

<a href="{{ route('admin.pedidos.index') }}"
   class="text-sm text-blue-600 hover:underline mb-4 inline-block">
    ← Voltar aos pedidos
</a>

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-3 text-left">Imagem</th>
                <th class="p-3 text-left">Foto ID</th>
                <th class="p-3 text-left">Galeria</th>
                <th class="p-3 text-right">Valor unitário</th>
                <th class="p-3 text-right">Quantidade</th>
                <th class="p-3 text-right">Subtotal</th>
            </tr>
        </thead>

        <tbody>
        @foreach($itens as $item)
            @php
                $valor = $item->foto->galeria->valor_foto ?? 0;
                $qtd = property_exists($item, 'quantidade')
                    ? max(1, $item->quantidade)
                    : 1;
            @endphp
            <tr class="border-t align-middle">

                {{-- IMAGE --}}
                <td class="p-3">
                    @if(!empty($item->foto->caminho_original))
                        <a href="{{ $item->foto->url_original }}" target="_blank">
                            <img src="{{ $item->foto->url_original }}"
                                class="h-16 w-auto rounded border hover:opacity-90">
                        </a>
                    @else
                        —
                    @endif
                </td>

                {{-- FOTO ID --}}
                <td class="p-3">{{ $item->foto->id }}</td>

                {{-- GALERIA --}}
                <td class="p-3">
                    {{ $item->foto->galeria->nome ?? '—' }}
                </td>

                {{-- VALOR UNITÁRIO --}}
                <td class="p-3 text-right">
                    R$ {{ number_format($valor, 2, ',', '.') }}
                </td>

                {{-- QUANTIDADE --}}
                <td class="p-3 text-right">{{ $qtd }}</td>

                {{-- SUBTOTAL --}}
                <td class="p-3 text-right font-medium">
                    R$ {{ number_format($valor * $qtd, 2, ',', '.') }}
                </td>

            </tr>
        @endforeach
        </tbody>


        <tfoot>
            <tr class="border-t bg-gray-50">
                <th colspan="5" class="p-3 text-right">Total</th>
                <th class="p-3 text-right font-bold">
                    R$ {{ number_format($totalCalculado, 2, ',', '.') }}
                </th>
            </tr>
        </tfoot>

    </table>
</div>

@endsection
