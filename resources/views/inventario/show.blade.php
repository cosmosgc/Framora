@extends('layouts.app')

@section('title', 'Detalhe Inventário')

@section('content')
<h1 class="h4 mb-3">Detalhe do Item</h1>

<div id="alert-placeholder"></div>

<div id="inventario-detail">
  <div class="text-muted">Carregando...</div>
</div>

<a href="{{ route('inventario.index') }}" class="btn btn-link mt-3">Voltar</a>
@endsection

<script>

const apiBase = "{{ url('/api') }}";
const id = {{ json_encode($id) }};

async function loadDetail() {
  const c = document.getElementById('inventario-detail');
  c.innerHTML = '<div class="text-muted">Carregando...</div>';
  try {
    const res = await fetch(`${apiBase}/inventario/${id}`, { headers: {'Accept': 'application/json'} });
    if (!res.ok) throw new Error('Item não encontrado');
    const item = await res.json();
    c.innerHTML = `
      <div class="card">
        <div class="card-body">
          <h5>Item #${item.id}</h5>
          <p><strong>Foto ID:</strong> ${item.foto_id ?? '-'}</p>
          <p><strong>Pedido ID:</strong> ${item.pedido_id ?? '-'}</p>
          <p><strong>Usuário:</strong> ${item.user_id ?? '-'}</p>
        </div>
      </div>
    `;
  } catch (e) {
    c.innerHTML = `<div class="alert alert-danger">${e.message}</div>`;
  }
}

document.addEventListener('DOMContentLoaded', loadDetail);
</script>