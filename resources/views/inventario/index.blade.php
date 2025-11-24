@extends('layouts.app')

@section('title', 'Meu Inventário')

@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-xl font-semibold">Meu Inventário</h1>

  <div class="flex items-center gap-2">
    <select id="filterStatus" class="rounded border px-2 py-1 text-sm">
      <option value="">Todos status</option>
      <option value="pago">Pago</option>
      <option value="pendente">Pendente</option>
      <option value="cancelado">Cancelado</option>
      <option value="reembolsado">Reembolsado</option>
    </select>

    <select id="filterGaleria" class="rounded border px-2 py-1 text-sm">
      <option value="">Todas galerias</option>
      @foreach($galerias as $g)
        <option value="{{ $g }}">{{ $g }}</option>
      @endforeach
    </select>

    <select id="orderBy" class="rounded border px-2 py-1 text-sm">
      <option value="newest">Mais recentes</option>
      <option value="oldest">Mais antigos</option>
    </select>
  </div>
</div>

<div class="flex gap-2 items-center mb-4">
  <input id="searchTerm" type="text" placeholder="Pesquisar id, foto..." class="flex-1 rounded border px-3 py-2 text-sm">
  <button id="btnApply" class="bg-blue-600 text-white px-3 py-2 rounded text-sm">Aplicar</button>
  <button id="btnClear" class="border px-3 py-2 rounded text-sm">Limpar</button>
</div>

<div id="alert-placeholder" class="mb-4"></div>

<div id="inventario-list">
  @if(count($grouped) === 0)
    <div class="text-gray-500">Inventário vazio.</div>
  @else
    @php $baseURL = url('/'); @endphp
    @foreach($grouped as $gal => $items)
      @include('inventario.components._gallery_grid', ['galeriaName' => $gal, 'items' => $items, 'baseURL' => $baseURL])
    @endforeach
  @endif
</div>

<!-- Modal: viewer (Tailwind) -->
<div id="galleryModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70">
  <div class="relative w-[95%] md:w-3/4 lg:w-2/3 max-h-[90vh] bg-neutral-900 rounded">
    <div class="flex items-center justify-between p-3 border-b border-neutral-700">
      <div id="galleryTitle" class="text-white text-sm"></div>
      <div class="flex items-center gap-2">
        <button id="zoomToggle" class="px-2 py-1 bg-white/10 text-white text-sm rounded">Zoom</button>
        <button id="downloadBtn" class="px-2 py-1 bg-white/10 text-white text-sm rounded">Download</button>
        <button id="closeModalBtn" class="px-2 py-1 bg-white/10 text-white text-sm rounded">Fechar</button>
      </div>
    </div>

    <div class="flex items-center justify-center p-3 relative">
      <button id="prevBtn" class="absolute left-3 top-1/2 -translate-y-1/2 bg-white/10 text-white px-2 py-1 rounded">◀</button>
      <div id="galleryImageContainer" class="max-h-[70vh] overflow-hidden flex items-center justify-center w-full">
        <img id="galleryImage" src="" alt="" class="max-w-full max-h-[70vh] transition-transform duration-150" />
      </div>
      <button id="nextBtn" class="absolute right-3 top-1/2 -translate-y-1/2 bg-white/10 text-white px-2 py-1 rounded">▶</button>
    </div>

    <div class="p-3 text-xs text-neutral-300 border-t border-neutral-700 flex items-center justify-between">
      <div id="galleryMeta"></div>
      <div id="galleryPos" class="text-neutral-400 text-xs"></div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
const baseURL = "{{ url('/') }}";
// itemsJson foi gerado no controller
const RAW_ITEMS = {!! $itemsJson !!};
let FILTERED = RAW_ITEMS.slice();
let FLAT = [];
let currentIndex = -1;
let zoomed = false;

document.addEventListener('DOMContentLoaded', () => {
  // wire up controls
  document.getElementById('btnApply').addEventListener('click', applyFilters);
  document.getElementById('btnClear').addEventListener('click', clearFilters);
  document.getElementById('searchTerm').addEventListener('keyup', (e)=> { if(e.key === 'Enter') applyFilters(); });
  // modal controls
  document.getElementById('closeModalBtn').addEventListener('click', closeModal);
  document.getElementById('zoomToggle').addEventListener('click', toggleZoom);
  document.getElementById('prevBtn').addEventListener('click', showPrev);
  document.getElementById('nextBtn').addEventListener('click', showNext);
  document.getElementById('downloadBtn').addEventListener('click', downloadCurrent);

  // build FLAT and attach click handlers to images (progressive enhancement)
  attachImageOpenHandlers();

  // keyboard
  window.addEventListener('keydown', (e) => {
    const modal = document.getElementById('galleryModal');
    if (modal.classList.contains('hidden')) return;
    if (e.key === 'ArrowLeft') showPrev();
    if (e.key === 'ArrowRight') showNext();
    if (e.key === 'Escape') closeModal();
  });
});

function attachImageOpenHandlers() {
  FLAT = RAW_ITEMS.slice(); // order as returned by server (grouped server-side)
  // Find all image buttons in the page and set click to open the modal with index from FLAT
  // images were rendered server-side; we search for img elements with data-original or alt containing foto id
  const imgs = document.querySelectorAll('#inventario-list img');
  imgs.forEach(img => {
    img.style.cursor = 'pointer';
    img.addEventListener('click', () => {
      // try to find item by foto_id or data-original
      const fotoIdMatch = img.alt?.match(/\d+/);
      let matchId = null;
      if (fotoIdMatch) matchId = Number(fotoIdMatch[0]);
      // find first matching item
      let idx = FLAT.findIndex(it => it.foto_id === matchId || it.id === matchId);
      if (idx === -1) {
        // fallback: try match by original path
        const orig = img.dataset.original;
        idx = FLAT.findIndex(it => (it.foto && (baseURL + '/' + it.foto.caminho_original) === orig));
      }
      if (idx === -1) {
        showAlert('warning', 'Não foi possível encontrar o item para visualizar.');
        return;
      }
      openModal(idx);
    });
  });
}

function showAlert(type, text) {
  const ph = document.getElementById('alert-placeholder');
  ph.innerHTML = `<div class="text-sm text-${type === 'danger' ? 'red-600' : 'yellow-600'}">${text}</div>`;
  setTimeout(()=> ph.innerHTML = '', 4000);
}

function applyFilters() {
  const status = document.getElementById('filterStatus').value;
  const gal = document.getElementById('filterGaleria').value;
  const order = document.getElementById('orderBy').value;
  const term = (document.getElementById('searchTerm').value || '').trim().toLowerCase();

  FILTERED = RAW_ITEMS.filter(i=>{
    if (status && ((i.pedido && (i.pedido.status_pedido || '') ) .toLowerCase()) !== status.toLowerCase()) return false;
    if (gal && ((i.foto && (i.foto.galeria || '')) !== gal)) return false;
    if (term) {
      const hay = `${i.id} ${i.foto_id} ${i.foto?.caminho_original ?? ''} ${i.foto?.titulo ?? ''}`.toLowerCase();
      if (!hay.includes(term)) return false;
    }
    return true;
  });

  FILTERED.sort((a,b) => {
    const ad = new Date(a.created_at || a.foto?.created_at || 0).getTime();
    const bd = new Date(b.created_at || b.foto?.created_at || 0).getTime();
    return (order === 'oldest') ? (ad - bd) : (bd - ad);
  });

  // Re-render only the grouped grid client-side for immediate response.
  rebuildGroupedView(FILTERED);
}

function clearFilters() {
  document.getElementById('filterStatus').value = '';
  document.getElementById('filterGaleria').value = '';
  document.getElementById('orderBy').value = 'newest';
  document.getElementById('searchTerm').value = '';
  FILTERED = RAW_ITEMS.slice();
  rebuildGroupedView(FILTERED);
}

function rebuildGroupedView(items) {
  // group by foto.galeria (server used same key)
  const container = document.getElementById('inventario-list');
  container.innerHTML = '';

  const grouped = {};
  items.forEach(it => {
    const gal = it.foto?.galeria || 'Sem galeria';
    grouped[gal] ||= [];
    grouped[gal].push(it);
  });

  if (Object.keys(grouped).length === 0) {
    container.innerHTML = `<div class="text-gray-500">Nenhum item encontrado.</div>`;
    return;
  }

  Object.entries(grouped).forEach(([gal, list]) => {
    const section = document.createElement('section');
    section.className = 'mb-6';
    section.innerHTML = `
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-semibold">${escapeHtml(gal)}</h3>
        <div class="text-xs text-gray-500">${list.length} itens</div>
      </div>
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3"></div>
    `;
    const grid = section.querySelector('div.grid');
    list.forEach(item => {
      const el = buildCardHtml(item);
      grid.insertAdjacentHTML('beforeend', el);
    });
    container.appendChild(section);
  });

  // Reattach handlers for newly created DOM images
  attachImageOpenHandlers();
}

function buildCardHtml(item) {
  const status = item.pedido?.status_pedido ?? null;
  const notPago = status !== 'pago';
  const foto = item.foto || {};
  const src = baseURL + '/' + (notPago ? (foto.caminho_thumb || foto.caminho_foto || foto.caminho_original || '') : (foto.caminho_original || foto.caminho_foto || foto.caminho_thumb || '') );
  const original = baseURL + '/' + (foto.caminho_original || '');
  return `
    <div class="bg-white rounded-lg overflow-hidden shadow-sm h-full">
      <div class="relative">
        ${notPago ? `<span class="absolute left-2 top-2 bg-yellow-300 text-yellow-900 text-xs px-2 py-1 rounded">${escapeHtml(status || 'não pago')}</span>` : ''}
        <button type="button" class="block w-full aspect-[4/3] overflow-hidden" onclick="">
          <img loading="lazy" src="${escapeAttr(src)}" data-original="${escapeAttr(original)}" alt="Foto ${item.foto_id}" class="w-full h-full object-cover transform hover:scale-105 transition" />
        </button>
      </div>
      <div class="p-2 text-xs flex justify-between items-start">
        <div>
          <div class="font-medium">ID ${item.id}</div>
          <div class="text-gray-500">Foto ${item.foto_id}</div>
        </div>
        <div class="text-right text-gray-500">
          <div>Pedido: ${item.pedido_id ?? '—'}</div>
        </div>
      </div>
    </div>
  `;
}

function escapeHtml(s) { return (s ?? '').toString().replace(/[&<>"'`]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','`':'&#96;'}[c])); }
function escapeAttr(s){ return escapeHtml(s); }

/* Modal logic */
function openModal(index) {
  currentIndex = index;
  showModalForIndex(index);
  document.getElementById('galleryModal').classList.remove('hidden');
  document.getElementById('galleryModal').classList.add('flex');
}

function closeModal() {
  document.getElementById('galleryModal').classList.add('hidden');
  document.getElementById('galleryModal').classList.remove('flex');
  const img = document.getElementById('galleryImage');
  img.style.transform = 'scale(1)';
  zoomed = false;
}

function showModalForIndex(idx) {
  const item = RAW_ITEMS[idx];
  if (!item) return;
  const foto = item.foto || {};
  const src = baseURL + '/' + (foto.caminho_original || foto.caminho_foto || foto.caminho_thumb || '');
  const title = foto.titulo || `Foto ${item.foto_id || item.id}`;
  document.getElementById('galleryTitle').innerText = title;
  const img = document.getElementById('galleryImage');
  img.src = src;
  img.dataset.currentIndex = idx;
  document.getElementById('galleryMeta').innerText = `ID ${item.id} • Foto ${item.foto_id} • Pedido ${item.pedido_id ?? '—'} • Status: ${item.pedido?.status_pedido ?? '—'}`;
  document.getElementById('galleryPos').innerText = `${idx + 1} / ${RAW_ITEMS.length}`;
  console.log('Showing item', idx, item);
  const naoPago = item.pedido.status_pedido !== 'pago';
  if(naoPago){
    document.getElementById('zoomToggle').classList.add('hidden');
    document.getElementById('downloadBtn').classList.add('hidden');
  } else {
    document.getElementById('zoomToggle').classList.remove('hidden');
    document.getElementById('downloadBtn').classList.remove('hidden');
  }
  // preload neighbors
  preloadImage(idx + 1);
  preloadImage(idx - 1);
}

function preloadImage(idx) {
  if (idx < 0 || idx >= RAW_ITEMS.length) return;
  const f = RAW_ITEMS[idx];
  const s = baseURL + '/' + (f.foto?.caminho_original || f.foto?.caminho_foto || f.foto?.caminho_thumb || '');
  const i = new Image();
  i.src = s;
}

function showPrev() {
  const idx = Number(document.getElementById('galleryImage').dataset.currentIndex || -1);
  if (idx <= 0) return;
  const next = idx - 1;
  showModalForIndex(next);
}
function showNext() {
  const idx = Number(document.getElementById('galleryImage').dataset.currentIndex || -1);
  if (idx >= RAW_ITEMS.length - 1) return;
  const next = idx + 1;
  showModalForIndex(next);
}

function toggleZoom() {
  const img = document.getElementById('galleryImage');
  zoomed = !zoomed;
  img.style.transform = zoomed ? 'scale(2)' : 'scale(1)';
}

function downloadCurrent() {
  const idx = Number(document.getElementById('galleryImage').dataset.currentIndex || -1);
  if (idx < 0) return;
  const item = RAW_ITEMS[idx];
  const src = baseURL + '/' + (item.foto?.caminho_original || item.foto?.caminho_foto || item.foto?.caminho_thumb || '');
  const a = document.createElement('a');
  a.href = src;
  a.download = src.split('/').pop();
  document.body.appendChild(a);
  a.click();
  a.remove();
}
</script>

<style>
/* small helpers for modal (tailwind is used in classes, here just small fallback) */
#galleryModal img { max-width: 100%; max-height: 70vh; }
</style>
@endpush
@stack('scripts')