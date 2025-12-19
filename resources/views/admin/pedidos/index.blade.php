@extends('layouts.admin')

@section('title', 'Pedidos')
@section('header', 'Pedidos')

@section('content')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet"
      href="https://cdn.datatables.net/2.3.5/css/dataTables.dataTables.min.css">

<table id="pedidosTable" class="display w-full">
    <thead>
        <tr>
            <th>ID</th>
            <th>Usuário</th>
            <th>Status</th>
            <th>Origem</th>
            <th>Pagamento</th>
            <th>Total (calculado)</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pedidos as $pedido)
            <tr>
                <td>#{{ $pedido->id }}</td>
                <td>{{ $pedido->user->name ?? '—' }}</td>
                <td>{{ ucfirst($pedido->status_pedido) }}</td>
                <td>
                    @if($pedido->source === 'inventario')
                        <span class="text-xs text-blue-600">Inventário</span>
                    @else
                        <span class="text-xs text-gray-600">Carrinho</span>
                    @endif
                </td>

                <td>{{ ucfirst($pedido->forma_pagamento) }}</td>
                <td>
                    R$ {{ number_format($pedido->calculated_total, 2, ',', '.') }}
                </td>
                <td>
                    <a href="{{ route('admin.pedidos.show', $pedido->id) }}"
                       class="text-blue-600 hover:underline">
                        Ver detalhes
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<script src="https://cdn.datatables.net/2.3.5/js/dataTables.min.js"></script>
<script>
new DataTable('#pedidosTable', {
    pageLength: 10,
    order: [[0, 'desc']],
    language: {
        search: "Buscar:",
        lengthMenu: "Mostrar _MENU_ pedidos",
        info: "Mostrando _START_ a _END_ de _TOTAL_ pedidos"
    }
});
</script>

@endsection
