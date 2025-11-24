@php
// espera: $galeriaName, $items, $baseURL
@endphp

<section class="mb-6">
  <div class="flex items-center justify-between mb-3">
    <h3 class="text-sm font-semibold">{{ $galeriaName }}</h3>
    <div class="text-xs text-gray-500">{{ count($items) }} itens</div>
  </div>

  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
    @foreach($items as $item)
      @include('inventario.components._item_card', ['item' => $item, 'baseURL' => $baseURL])
    @endforeach
  </div>
</section>
