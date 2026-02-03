@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<h1 class="h4 mb-3">Checkout</h1>

<div id="alert-placeholder"></div>

<form id="checkoutForm">
  <div class="mb-3">
    <label class="form-label">Forma de pagamento</label>
    <select name="forma_pagamento" id="forma_pagamento" class="form-select" required>
      <option value="card">Cartão</option>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Itens do pedido (IDs de foto)</label>
    <small class="form-text text-muted">Adicione os foto_id separados por vírgula para simular o carrinho</small>
    <input type="text" id="items_input" class="form-control" placeholder="ex: 12,34,55">
  </div>

  <div class="mb-3">
    <label class="form-label">Valor total (R$)</label>
    <input type="number" step="0.01" id="valor_total" class="form-control" required>
  </div>

  <button type="submit" class="btn btn-primary">Criar pedido (simulação)</button>

  <!-- Placeholder Stripe -->
  <button type="button" class="btn btn-outline-secondary ms-2" id="stripeButton" disabled>
    Pagar com Stripe (desabilitado - a implementar)
  </button>
</form>


<script>
const apiBase = "{{ url('/api') }}";
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

document.getElementById('checkoutForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const forma = document.getElementById('forma_pagamento').value;
  const itemsRaw = document.getElementById('items_input').value.trim();
  const valor_total = document.getElementById('valor_total').value;

  // transformar em array de objetos {foto_id: X}
  const items = itemsRaw.length ? itemsRaw.split(',').map(s => ({ foto_id: Number(s.trim()) })) : [];

  try {
    const res = await fetch(`${apiBase}/pedidos`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({
        forma_pagamento: forma,
        valor_total: Number(valor_total),
        items: items
      })
    });
    const json = await res.json();
    if (!res.ok) throw new Error(json.message || 'Erro ao criar pedido');
    showAlert('success', 'Pedido criado com sucesso! ID: ' + json.data.id);
    // redirecionar para pedidos
    setTimeout(()=> location.href = '/pedidos', 1200);
  } catch (err) {
    showAlert('danger', err.message);
  }
});

function showAlert(type, text) {
  const ph = document.getElementById('alert-placeholder');
  ph.innerHTML = `<div class="alert alert-${type}">${text}</div>`;
  setTimeout(()=> ph.innerHTML = '', 5000);
}
</script>
@endsection