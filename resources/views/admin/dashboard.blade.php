@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header', 'Dashboard')

@section('content')

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white p-4 rounded shadow">
        <p class="text-sm text-gray-500">Galerias</p>
        <p class="text-2xl font-bold">{{ $totalGalerias }}</p>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <p class="text-sm text-gray-500">Fotos</p>
        <p class="text-2xl font-bold">{{ $totalFotos }}</p>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <p class="text-sm text-gray-500">Pedidos</p>
        <p class="text-2xl font-bold">{{ $totalPedidos }}</p>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <p class="text-sm text-gray-500">Receita Total</p>
        <p class="text-2xl font-bold text-green-600">
            R$ {{ number_format($receitaTotal, 2, ',', '.') }}
        </p>
    </div>

</div>
<div class="bg-white p-6 rounded shadow mb-6">
    <h2 class="font-semibold mb-4">Pedidos por mês</h2>

    <canvas id="pedidosChart" height="100"></canvas>
</div>

<div class="bg-white rounded shadow overflow-x-auto">
    <h2 class="font-semibold p-4 border-b">Últimos pedidos</h2>

    <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-3 text-left">ID</th>
                <th class="p-3 text-left">Usuário</th>
                <th class="p-3 text-left">Status</th>
                <th class="p-3 text-left">Pagamento</th>
                <th class="p-3 text-right">Data</th>
                <th class="p-3 text-right">Valor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ultimosPedidos as $pedido)
                <tr class="border-t">
                    <td class="p-3">#{{ $pedido->id }}</td>
                    <td class="p-3">
                        {{ $pedido->user->name ?? '—' }}
                    </td>
                    <td class="p-3">
                        {{ ucfirst($pedido->status_pedido) }}
                    </td>
                    <td class="p-3">
                        {{ ucfirst($pedido->forma_pagamento) }}
                    </td>
                    <td class="p-3 text-right font-medium">
                        {{ $pedido->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="p-3 text-right font-medium">
                        R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">
                        Nenhum pedido encontrado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('pedidosChart');

const data = {
    labels: {!! json_encode($pedidosPorMes->pluck('mes')) !!},
    datasets: [
        {
            label: 'Pedidos',
            data: {!! json_encode($pedidosPorMes->pluck('total')) !!},
            borderWidth: 2,
            tension: 0.3
        },
        {
            label: 'Receita (R$)',
            data: {!! json_encode($pedidosPorMes->pluck('valor')) !!},
            borderWidth: 2,
            tension: 0.3,
            yAxisID: 'y1'
        }
    ]
};

new Chart(ctx, {
    type: 'line',
    data: data,
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true },
            y1: {
                beginAtZero: true,
                position: 'right',
                grid: { drawOnChartArea: false }
            }
        }
    }
});
</script>
@endpush
@stack('scripts')

@endsection
