@extends('layouts.app')

@section('title', 'Meus Pedidos')

@section('content')
<div class="space-y-8">
    <section class="app-shell px-6 py-8 sm:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl space-y-3">
                <p class="page-eyebrow">Pedidos</p>
                <h1 class="page-title">Acompanhe suas compras em um painel mais claro e consistente.</h1>
                <p class="page-copy">
                    Confira status, valor total e os itens recebidos sem depender de estilos misturados de Bootstrap e Tailwind.
                </p>
            </div>

            <a href="{{ route('pedidos.web.create') }}" class="btn-primary">Fazer checkout</a>
        </div>
    </section>

    <div id="alert-placeholder"></div>

    <section class="app-panel p-6 sm:p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold tracking-tight text-stone-950">Histórico recente</h2>
            <p class="mt-2 page-copy">Os pedidos mais recentes são carregados automaticamente.</p>
        </div>

        <div id="pedidos-list">
            <div class="app-panel-muted p-5 text-sm text-stone-600">Carregando pedidos...</div>
        </div>
    </section>
</div>

<script>
const apiBase = "{{ url('/api') }}";
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

async function loadPedidos() {
    const container = document.getElementById('pedidos-list');
    container.innerHTML = '<div class="app-panel-muted p-5 text-sm text-stone-600">Carregando pedidos...</div>';

    try {
        const res = await fetch(`${apiBase}/pedidos?per_page=20`, {
            headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) {
            throw new Error('Erro ao buscar pedidos');
        }

        const data = await res.json();
        if (!data.data || data.data.length === 0) {
            container.innerHTML = '<div class="empty-state"><p class="page-eyebrow">Sem pedidos</p><h3 class="mt-3 text-2xl font-semibold text-stone-950">Nenhum pedido encontrado.</h3><p class="mx-auto mt-3 max-w-xl page-copy">Assim que você concluir uma compra, ela aparece aqui para consulta rápida.</p></div>';
            return;
        }

        const rows = data.data.map(p => {
            const items = (p.inventario || []).map(i => `
                <li class="rounded-full bg-stone-100 px-3 py-1 text-xs font-medium text-stone-700">
                    ${i.foto_id ? 'Foto #' + i.foto_id : 'ID: ' + i.id}
                </li>
            `).join('');

            return `
                <article class="app-panel mb-4 p-5 sm:p-6">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        <div class="space-y-4">
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-cyan-800">
                                    Pedido #${p.id}
                                </span>
                                <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-medium uppercase tracking-[0.16em] text-stone-700">
                                    ${p.status_pedido}
                                </span>
                            </div>

                            <div>
                                <p class="text-sm text-stone-500">Valor total</p>
                                <p class="text-2xl font-semibold text-stone-950">R$ ${Number(p.valor_total).toFixed(2)}</p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-stone-700">Itens recebidos</p>
                                <ul class="mt-3 flex flex-wrap gap-2">${items || '<li class="text-sm text-stone-500">Sem itens vinculados.</li>'}</ul>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <a class="btn-secondary px-4 py-2.5" href="/inventario">Ver inventário</a>
                            <button class="btn-danger" onclick="deletarPedido(${p.id})">Remover</button>
                        </div>
                    </div>
                </article>
            `;
        }).join('');

        container.innerHTML = rows;
    } catch (err) {
        container.innerHTML = `<div class="app-alert app-alert-error">Erro: ${err.message}</div>`;
    }
}

async function deletarPedido(id) {
    if (!confirm('Deseja remover este pedido?')) {
        return;
    }

    try {
        const res = await fetch(`${apiBase}/pedidos/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        if (!res.ok) {
            throw new Error('Falha ao remover pedido');
        }

        const json = await res.json();
        showAlert('success', json.message || 'Pedido removido');
        loadPedidos();
    } catch (e) {
        showAlert('error', e.message);
    }
}

function showAlert(type, text) {
    const ph = document.getElementById('alert-placeholder');
    ph.innerHTML = `<div class="app-alert ${type === 'success' ? 'app-alert-success' : 'app-alert-error'}">${text}</div>`;
    setTimeout(() => ph.innerHTML = '', 5000);
}

document.addEventListener('DOMContentLoaded', loadPedidos);
</script>

@endsection
