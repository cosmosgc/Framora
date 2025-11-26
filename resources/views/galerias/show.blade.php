@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">

    <div class="flex items-center justify-between mb-4">
        <h1 class="text-3xl font-bold">{{ $galeria->nome }}</h1>

        {{-- Botão só para o dono da galeria --}}
        @if(auth()->check() && $galeria->user_id == auth()->id())
            <a href="{{ route('galerias.web.edit', $galeria->id) }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                Editar Galeria
            </a>
        @endif
    </div>

    @if($galeria->categoria)
        <p class="text-gray-600 mb-2">
            Categoria: <strong>{{ $galeria->categoria->nome }}</strong>
        </p>
    @endif

    @if($galeria->descricao)
        <p class="mb-6 text-gray-700">{{ $galeria->descricao }}</p>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="galleryGrid">
        @forelse($galeria->fotos as $i => $foto)
            @include('galerias.components.foto_item', ['foto' => $foto, 'index' => $i])
        @empty
            <p class="text-gray-500 col-span-full text-center">Nenhuma foto disponível nesta galeria.</p>
        @endforelse
    </div>
</div>

@include('galerias.partials.gallery_modal') {{-- modal em partial (abaixo) --}}

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Modal elements ---
    const modal = document.getElementById('galleryModal');
    const galleryImage = document.getElementById('galleryImage');
    const galleryImageContainer = document.getElementById('galleryImageContainer');
    const galleryTitle = document.getElementById('galleryTitle');
    const galleryMeta = document.getElementById('galleryMeta');
    const galleryPos = document.getElementById('galleryPos');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const zoomToggle = document.getElementById('zoomToggle');
    const downloadBtn = document.getElementById('downloadBtn');

    // build an array of items from the grid
    const items = Array.from(document.querySelectorAll('#galleryGrid .foto-open-btn'))
        // normalize duplicates (buttons inside components and image wrappers)
        .map(btn => ({
            src: btn.dataset.src,
            thumb: btn.dataset.thumb,
            title: btn.dataset.title,
            desc: btn.dataset.desc
        }));

    let currentIndex = 0;
    let zoomed = false;

    function openModal(index) {
        currentIndex = index;
        const item = items[index];
        if (!item) return;
        galleryImage.src = item.src;
        galleryTitle.textContent = item.title || '';
        galleryMeta.textContent = item.desc || '';
        galleryPos.textContent = (index + 1) + ' / ' + items.length;
        galleryImage.classList.remove('scale-125');
        zoomed = false;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    function next() {
        currentIndex = (currentIndex + 1) % items.length;
        openModal(currentIndex);
    }
    function prev() {
        currentIndex = (currentIndex - 1 + items.length) % items.length;
        openModal(currentIndex);
    }

    // event delegation for open buttons
    document.body.addEventListener('click', function (ev) {
        const btn = ev.target.closest('.foto-open-btn');
        if (!btn) return;
        ev.preventDefault();
        const idx = parseInt(btn.dataset.index, 10) || 0;
        openModal(idx);
    });

    // modal controls
    nextBtn.addEventListener('click', next);
    prevBtn.addEventListener('click', prev);
    closeModalBtn.addEventListener('click', closeModal);

    // keyboard navigation
    document.addEventListener('keydown', function (e) {
        if (modal.classList.contains('hidden')) return;
        if (e.key === 'ArrowRight') next();
        if (e.key === 'ArrowLeft') prev();
        if (e.key === 'Escape') closeModal();
        if (e.key === ' ') { // space to toggle zoom
            e.preventDefault();
            toggleZoom();
        }
    });

    function toggleZoom() {
        zoomed = !zoomed;
        if (zoomed) {
            galleryImage.classList.add('scale-125');
            zoomToggle.textContent = 'Unzoom';
        } else {
            galleryImage.classList.remove('scale-125');
            zoomToggle.textContent = 'Zoom';
        }
    }
    zoomToggle.addEventListener('click', toggleZoom);

    // download
    downloadBtn.addEventListener('click', function () {
        const link = document.createElement('a');
        link.href = galleryImage.src;
        // tenta extrair filename
        const parts = galleryImage.src.split('/');
        link.download = parts[parts.length - 1] || 'foto';
        document.body.appendChild(link);
        link.click();
        link.remove();
    });

    // clique fora fecha (overlay)
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    // --- Add to cart via AJAX (intercepta formulários .add-to-cart-form) ---
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', async function (ev) {
            ev.preventDefault();
            const url = this.action;
            const formData = new FormData(this);

            // opcional: mostrar loading
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn?.textContent;
            if (btn) { btn.disabled = true; btn.textContent = 'Adicionando...'; }

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch(url, {
                    method: 'POST',
                    headers: token ? { 'X-CSRF-TOKEN': token } : {},
                    body: formData,
                });
                const data = await res.json().catch(()=>({}));
                if (res.ok) {
                    // feedback simples
                    alert('Foto adicionada ao carrinho');
                } else {
                    alert(data.message ?? 'Erro ao adicionar ao carrinho');
                }
            } catch (err) {
                console.error(err);
                alert('Erro de rede ao adicionar ao carrinho');
            } finally {
                if (btn) { btn.disabled = false; btn.textContent = originalText; }
            }
        });
    });

});
</script>
@endpush
@stack('scripts')