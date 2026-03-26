@extends('layouts.app')

@section('content')
@php
    $avatar = $galeria->user?->avatar;
    $avatarUrl = $avatar
        ? (\Illuminate\Support\Str::startsWith($avatar, ['http://', 'https://'])
            ? $avatar
            : asset($avatar))
        : null;
    $userInitial = strtoupper(substr($galeria->user->name ?? 'U', 0, 1));
    $bannerUrl = $galeria->banner?->imagem ? asset($galeria->banner->imagem) : null;
    $totalFotos = $galeria->fotos->count();
@endphp

<div class="mx-auto max-w-7xl space-y-8 px-4 py-6 sm:px-6 lg:px-8">
    <section class="overflow-hidden rounded-[2rem] border border-stone-200 bg-stone-950 text-white shadow-[0_35px_90px_-40px_rgba(15,23,42,0.82)]">
        <div class="relative min-h-[30rem]">
            @if($bannerUrl)
                <img
                    src="{{ $bannerUrl }}"
                    alt="Banner da galeria {{ $galeria->nome }}"
                    class="absolute inset-0 h-full w-full object-cover opacity-35"
                >
            @endif

            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(250,204,21,0.26),_transparent_28%),linear-gradient(135deg,rgba(12,10,9,0.88),rgba(41,37,36,0.72),rgba(8,145,178,0.45))]"></div>

            <div class="relative grid gap-8 px-6 py-8 lg:grid-cols-[minmax(0,1.2fr)_340px] lg:px-10 lg:py-10">
                <div class="space-y-6">
                    <div class="flex flex-wrap items-center gap-3">
                        @if($galeria->categoria)
                            <span class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.28em] text-amber-100 backdrop-blur">
                                {{ $galeria->categoria->nome }}
                            </span>
                        @endif
                        <span class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.28em] text-white/75 backdrop-blur">
                            {{ $totalFotos }} {{ \Illuminate\Support\Str::plural('foto', $totalFotos) }}
                        </span>
                        @if($galeria->data)
                            <span class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.28em] text-white/75 backdrop-blur">
                                {{ \Carbon\Carbon::parse($galeria->data)->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <h1 class="max-w-4xl text-4xl font-semibold tracking-tight sm:text-5xl lg:text-6xl">
                            {{ $galeria->nome }}
                        </h1>

                        @if($galeria->descricao)
                            <p class="max-w-3xl text-base leading-7 text-white/78 sm:text-lg">
                                {{ $galeria->descricao }}
                            </p>
                        @else
                            <p class="max-w-3xl text-base leading-7 text-white/72 sm:text-lg">
                                Uma colecao publicada para reunir imagens, atmosfera e narrativa visual em uma apresentacao mais editorial.
                            </p>
                        @endif
                    </div>

                    <div class="flex flex-wrap gap-3">
                        @if(auth()->check() && $galeria->user_id == auth()->id())
                            <a href="{{ route('galerias.web.edit', $galeria->id) }}"
                               class="inline-flex items-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-stone-900 transition hover:bg-stone-200">
                                Editar galeria
                            </a>
                        @endif

                        <a href="#galleryGrid"
                           class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-5 py-3 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/20">
                            Ver fotos
                        </a>
                    </div>
                </div>

                <div class="space-y-4">
                    @if($galeria->user)
                        <div class="rounded-[1.75rem] border border-white/10 bg-white/10 p-5 backdrop-blur">
                            <p class="text-xs uppercase tracking-[0.28em] text-white/55">Publicado por</p>
                            <div class="mt-4 flex items-center gap-4">
                                <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-full border border-white/15 bg-white/15 text-lg font-semibold text-white">
                                    @if($avatarUrl)
                                        <img src="{{ $avatarUrl }}" alt="Avatar de {{ $galeria->user->name }}" class="h-full w-full object-cover">
                                    @else
                                        {{ $userInitial }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('profiles.show', $galeria->user) }}" class="block truncate text-xl font-semibold text-white transition hover:text-amber-200">
                                        {{ $galeria->user->name }}
                                    </a>
                                    <p class="mt-1 text-sm text-white/65">Perfil publico do criador</p>
                                </div>
                            </div>

                            @if($galeria->user->bio)
                                <p class="mt-4 text-sm leading-6 text-white/72">
                                    {{ \Illuminate\Support\Str::limit($galeria->user->bio, 180) }}
                                </p>
                            @endif
                        </div>
                    @endif

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                        <div class="rounded-[1.75rem] border border-white/10 bg-white/10 p-5 backdrop-blur">
                            <p class="text-xs uppercase tracking-[0.24em] text-white/55">Local</p>
                            <p class="mt-3 text-2xl font-semibold">{{ $galeria->local ?: '--' }}</p>
                        </div>
                        <div class="rounded-[1.75rem] border border-white/10 bg-white/10 p-5 backdrop-blur">
                            <p class="text-xs uppercase tracking-[0.24em] text-white/55">Valor por foto</p>
                            <p class="mt-3 text-2xl font-semibold">
                                @if(!is_null($galeria->valor_foto))
                                    R$ {{ number_format($galeria->valor_foto, 2, ',', '.') }}
                                @else
                                    --
                                @endif
                            </p>
                        </div>
                        <div class="rounded-[1.75rem] border border-white/10 bg-white/10 p-5 backdrop-blur">
                            <p class="text-xs uppercase tracking-[0.24em] text-white/55">Duracao</p>
                            <p class="mt-3 text-2xl font-semibold">{{ $galeria->tempo_duracao ?: '--' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">
        <div class="space-y-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">Conteudo da publicacao</p>
                    <h2 class="mt-2 text-3xl font-semibold tracking-tight text-stone-900">Galeria em destaque</h2>
                </div>
                <div class="rounded-full bg-stone-100 px-4 py-2 text-sm text-stone-600">
                    Clique em qualquer foto para abrir o viewer
                </div>
            </div>

            <div id="galleryGrid" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @forelse($galeria->fotos as $i => $foto)
                    @include('galerias.components.foto_item', ['foto' => $foto, 'index' => $i])
                @empty
                    <div class="col-span-full rounded-[2rem] border border-dashed border-stone-300 bg-[linear-gradient(135deg,#fafaf9,#f5f5f4,#ecfeff)] px-8 py-16 text-center">
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-cyan-700">Sem fotos publicadas</p>
                        <h3 class="mt-3 text-2xl font-semibold text-stone-900">Esta galeria ainda esta sendo montada.</h3>
                        <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-stone-600">
                            Assim que novas imagens forem adicionadas, elas vao aparecer aqui como conteudo principal da publicacao.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        <aside class="space-y-5">
            <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-[0_20px_50px_-35px_rgba(28,25,23,0.45)]">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-amber-700">Resumo</p>
                <dl class="mt-5 space-y-4 text-sm text-stone-600">
                    <div class="flex items-center justify-between gap-4 border-b border-stone-100 pb-4">
                        <dt>Galeria</dt>
                        <dd class="text-right font-semibold text-stone-900">{{ $galeria->nome }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 border-b border-stone-100 pb-4">
                        <dt>Categoria</dt>
                        <dd class="text-right font-semibold text-stone-900">{{ $galeria->categoria->nome ?? '--' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 border-b border-stone-100 pb-4">
                        <dt>Fotos</dt>
                        <dd class="text-right font-semibold text-stone-900">{{ $totalFotos }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt>Autor</dt>
                        <dd class="text-right font-semibold text-stone-900">{{ $galeria->user->name ?? '--' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-[1.75rem] border border-emerald-200 bg-[linear-gradient(135deg,#f0fdf4,#ffffff,#ecfeff)] p-6 shadow-[0_20px_50px_-35px_rgba(13,148,136,0.35)]">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-emerald-700">Leitura da pagina</p>
                <h3 class="mt-3 text-2xl font-semibold text-stone-900">Capa em cima, conteudo embaixo.</h3>
                <p class="mt-3 text-sm leading-7 text-stone-700">
                    Esta versao segue a mesma logica da tela de criacao: o banner apresenta a galeria, enquanto as fotos aparecem separadas como a parte principal da experiencia.
                </p>
            </div>
        </aside>
    </section>
</div>

@include('galerias.partials.gallery_modal')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
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

    const items = Array.from(document.querySelectorAll('#galleryGrid .foto-open-btn'))
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

    document.body.addEventListener('click', function (ev) {
        const btn = ev.target.closest('.foto-open-btn');
        if (!btn) return;
        ev.preventDefault();
        const idx = parseInt(btn.dataset.index, 10) || 0;
        openModal(idx);
    });

    nextBtn.addEventListener('click', next);
    prevBtn.addEventListener('click', prev);
    closeModalBtn.addEventListener('click', closeModal);

    document.addEventListener('keydown', function (e) {
        if (modal.classList.contains('hidden')) return;
        if (e.key === 'ArrowRight') next();
        if (e.key === 'ArrowLeft') prev();
        if (e.key === 'Escape') closeModal();
        if (e.key === ' ') {
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

    downloadBtn.addEventListener('click', function () {
        const link = document.createElement('a');
        link.href = galleryImage.src;
        const parts = galleryImage.src.split('/');
        link.download = parts[parts.length - 1] || 'foto';
        document.body.appendChild(link);
        link.click();
        link.remove();
    });

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', async function (ev) {
            ev.preventDefault();
            const url = this.action;
            const formData = new FormData(this);
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn?.textContent;
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Adicionando...';
            }

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch(url, {
                    method: 'POST',
                    headers: token ? { 'X-CSRF-TOKEN': token } : {},
                    body: formData,
                });
                const data = await res.json().catch(() => ({}));
                if (res.ok) {
                    alert('Foto adicionada ao carrinho');
                } else {
                    alert(data.message ?? 'Erro ao adicionar ao carrinho');
                }
            } catch (err) {
                console.error(err);
                alert('Erro de rede ao adicionar ao carrinho');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            }
        });
    });
});
</script>
@endpush
@stack('scripts')
