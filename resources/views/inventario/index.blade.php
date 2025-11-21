@extends('layouts.app')

@section('title', 'Meu Inventário')

@section('content')
<h1 class="h4 mb-3">Meu Inventário</h1>

<div id="alert-placeholder"></div>

<div id="inventario-list">
  <div class="text-muted">Carregando inventário...</div>
</div>
@endsection

<script>
const apiBase = "{{ url('/api') }}";


async function loadInventario() {
  const container = document.getElementById('inventario-list');
  container.innerHTML = '<div class="text-muted">Carregando inventário...</div>';
  try {
    const res = await fetch(`${apiBase}/inventario?per_page=50`, { headers: {'Accept': 'application/json'} });
    if (!res.ok) throw new Error('Erro ao buscar inventario');
    const data = await res.json();

    if (!data.data || data.data.length === 0) {
      container.innerHTML = '<div class="alert alert-info">Inventário vazio.</div>';
      return;
    }

    const cards = data.data.map(i => `
      <div class="card mb-3">
        <div class="card-body d-flex justify-content-between">
          <div>
            <h5>Item #${i.id}</h5>
            <div class="small text-muted">Foto: ${i.foto_id ?? '-'}</div>
            <div class="small text-muted">Pedido: ${i.pedido_id ?? '—'}</div>
          </div>
          <div class="text-end">
            <a class="btn btn-sm btn-outline-primary" href="/inventario/${i.id}">Ver</a>
            <button class="btn btn-sm btn-danger" onclick="deletarInventario(${i.id})">Remover</button>
          </div>
        </div>
      </div>
    `).join('');

    container.innerHTML = cards;
  } catch (e) {
    container.innerHTML = `<div class="alert alert-danger">${e.message}</div>`;
  }
}

async function deletarInventario(id) {
  if (!confirm('Remover item do inventário?')) return;
  try {
    const res = await fetch(`${apiBase}/inventario/${id}`, {
      method: 'DELETE',
      headers: {'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
    });
    if (!res.ok) throw new Error('Erro ao remover item');
    const json = await res.json();
    showAlert('success', json.message || 'Removido');
    loadInventario();
  } catch (err) {
    showAlert('danger', err.message);
  }
}

function showAlert(type, text) {
  const ph = document.getElementById('alert-placeholder');
  ph.innerHTML = `<div class="alert alert-${type}">${text}</div>`;
  setTimeout(()=> ph.innerHTML = '', 4000);
}

document.addEventListener('DOMContentLoaded', loadInventario);
</script>