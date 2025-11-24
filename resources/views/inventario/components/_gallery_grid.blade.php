@php
$bannerUrl = null;
if (!empty($items) && isset($items[0]->foto->banner)) {
    $bannerUrl = $items[0]->foto->banner->imagem ?? null;
}
$hasBanner = !empty($bannerUrl);
$bannerCssUrl = $hasBanner ? e($baseURL . '/' . ltrim($bannerUrl, '/')) : null;
@endphp

<section class="mb-6 rounded-md shadow-sm overflow-hidden" 
         @if($hasBanner) style="background-image:url('{{ $bannerCssUrl }}'); background-size:cover; background-position:center;" @endif>
  {{-- OVERLAY (essa DIV aplica o glass blur) --}}
  <div
    class="rounded-md p-3 border border-gray-100 bg-clip-padding bg-clip-padding backdrop-filter backdrop-blur-sm bg-opacity-10"
    
  >
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-sm font-semibold text-gray-900">{{ $galeriaName }}</h3>
      <div class="text-xs text-gray-600">{{ count($items) }} itens</div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
      @foreach($items as $item)
        @include('inventario.components._item_card', ['item' => $item, 'baseURL' => $baseURL])
      @endforeach
    </div>
  </div>
</section>
