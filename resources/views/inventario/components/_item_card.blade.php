@php
// espera: $item, $baseURL
$status = $item->pedido->status_pedido ?? null;
$notPago = $status !== 'pago';
$foto = $item->foto ?? null;
$src = $baseURL . '/' . ($notPago
    ? ($foto->caminho_thumb ?? $foto->caminho_foto ?? $foto->caminho_original)
    : ($foto->caminho_original ?? $foto->caminho_foto ?? $foto->caminho_thumb));
@endphp

<div class="bg-white rounded-lg overflow-hidden shadow-sm h-full">
  <div class="relative">
    @if($notPago)
      <span class="absolute left-2 top-2 bg-yellow-300 text-yellow-900 text-xs px-2 py-1 rounded">{{ $status ?? 'não pago' }}</span>
    @endif

    <button type="button" class="block w-full aspect-[4/3] overflow-hidden"  aria-label="Abrir visualizador">
      <img
        loading="lazy"
        src="{{ $src }}"
        data-original="{{ $baseURL . '/' . ($foto->caminho_original ?? '') }}"
        alt="Foto {{ $item->foto_id }}"
        class="w-full h-full object-cover transform hover:scale-105 transition"
      />
    </button>
  </div>

  <div class="p-2 text-xs flex justify-between items-start">
    <div>
      <div class="font-medium">ID {{ $item->id }}</div>
      <div class="text-gray-500">Foto {{ $item->foto_id }}</div>
    </div>
    <div class="text-right text-gray-500">
      <div>Pedido: {{ $item->pedido_id ?? '—' }}</div>
    </div>
  </div>
</div>
