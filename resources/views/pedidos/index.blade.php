@extends('layouts.app')

@section('title', 'Meus Pedidos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4">Meus Pedidos</h1>
  <a href="{{ route('pedidos.web.create') }}" class="btn btn-primary">Fazer Checkout</a>
</div>

<div id="alert-placeholder"></div>

<div id="pedidos-list">
  <div class="text-muted">Carregando pedidos...</div>
</div>


<script>
const apiBase = "{{ url('/api') }}";
// const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

async function loadPedidos() {
  const container = document.getElementById('pedidos-list');
  container.innerHTML = '<div class="text-muted">Carregando pedidos...</div>';

  try {
    const res = await fetch(`${apiBase}/pedidos?per_page=20`, {
      headers: { 'Accept': 'application/json' }
    });
    if (!res.ok) throw new Error('Erro ao buscar pedidos');

    const data = await res.json();
    if (!data.data || data.data.length === 0) {
      container.innerHTML = '<div class="alert alert-info">Nenhum pedido encontrado.</div>';
      return;
    }

    const rows = data.data.map(p => {
      const items = (p.inventario || []).map(i => `<li>${i.foto_id ? 'Foto #' + i.foto_id : 'ID: ' + i.id}</li>`).join('');
      return `
        <div class="card mb-3">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h5 class="card-title">Pedido #${p.id} — ${p.status_pedido}</h5>
                <div class="small text-muted">Valor: R$ ${Number(p.valor_total).toFixed(2)}</div>
                <ul class="mt-2">${items}</ul>
              </div>
              <div class="text-end">
                <a class="btn btn-sm btn-outline-secondary" href="/inventario">Ver inventário</a>
                <button class="btn btn-sm btn-danger" onclick="deletarPedido(${p.id})">Remover</button>
              </div>
            </div>
          </div>
        </div>
      `;
    }).join('');

    container.innerHTML = rows;
  } catch (err) {
    container.innerHTML = `<div class="alert alert-danger">Erro: ${err.message}</div>`;
  }
}

async function deletarPedido(id) {
  if (!confirm('Deseja remover este pedido?')) return;
  try {
    const res = await fetch(`${apiBase}/pedidos/${id}`, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      }
    });
    if (!res.ok) throw new Error('Falha ao remover pedido');
    const json = await res.json();
    showAlert('success', json.message || 'Pedido removido');
    loadPedidos();
  } catch (e) {
    showAlert('danger', e.message);
  }
}

function showAlert(type, text) {
  const ph = document.getElementById('alert-placeholder');
  ph.innerHTML = `<div class="alert alert-${type}">${text}</div>`;
  setTimeout(()=> ph.innerHTML = '', 5000);
}

document.addEventListener('DOMContentLoaded', loadPedidos);
</script>

@endsection