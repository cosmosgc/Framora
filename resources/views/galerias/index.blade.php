@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <section class="app-shell px-6 py-8 sm:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl space-y-3">
                <p class="page-eyebrow">Galerias</p>
                <h1 class="page-title">Explore coleções com uma apresentação mais limpa e direta.</h1>
                <p class="page-copy">
                    Esta página agora segue a mesma base visual do resto da aplicação, sem depender de uma hero section exagerada.
                </p>
            </div>

            <div id="favorito-feedback" class="hidden max-w-md"></div>
        </div>
    </section>

    <section class="app-panel p-6 sm:p-8">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight text-stone-950">Todas as galerias</h2>
                <p class="mt-2 page-copy">Carregadas em tempo real e prontas para favoritar.</p>
            </div>
        </div>

        <div id="galerias-list" class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3"></div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const BASE_URL = "{{ url('/') }}";
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const FAVORITOS_URL = "{{ route('favoritos.store') }}";
    const LOGIN_URL = "{{ route('login') }}";
    const IS_AUTHENTICATED = @json(auth()->check());
    const container = document.getElementById('galerias-list');
    const feedback = document.getElementById('favorito-feedback');
    container.innerHTML = '<div class="app-panel-muted p-5 text-sm text-stone-600">Carregando galerias...</div>';

    const showFeedback = (message, type = 'success') => {
        feedback.className = `app-alert ${
            type === 'error'
                ? 'app-alert-error'
                : type === 'info'
                    ? 'app-alert-info'
                    : 'app-alert-success'
        }`;
        feedback.textContent = message;
        feedback.classList.remove('hidden');
    };

    try {
        const response = await fetch(`${BASE_URL}/api/galerias`);
        const result = await response.json();

        if (!result.success) {
            container.innerHTML = '<div class="app-alert app-alert-error">Erro ao carregar galerias.</div>';
            return;
        }

        container.innerHTML = result.data.map(galeria => `
            <article class="overflow-hidden rounded-[1.75rem] border border-stone-200 bg-white shadow-[0_18px_38px_-30px_rgba(28,25,23,0.28)] transition hover:-translate-y-1 hover:shadow-[0_25px_50px_-32px_rgba(28,25,23,0.34)]">
                <div class="aspect-[4/3] overflow-hidden bg-stone-100">
                    <img src="${BASE_URL}/${galeria.banner?.imagem ?? 'placeholder.jpg'}" alt="${galeria.nome}" class="h-full w-full object-cover">
                </div>
                <div class="space-y-4 p-5">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">Galeria</p>
                        <h2 class="mt-2 text-xl font-semibold text-stone-950">${galeria.nome}</h2>
                        <p class="mt-2 line-clamp-3 text-sm leading-6 text-stone-600">${galeria.descricao ?? ''}</p>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <a href="${BASE_URL}/galerias/${galeria.id}" class="btn-secondary px-4 py-2.5">Ver detalhes</a>
                        <button
                            type="button"
                            class="btn-danger"
                            data-favorito-id="${galeria.id}"
                        >
                            Favoritar
                        </button>
                    </div>
                </div>
            </article>
        `).join('');

        container.addEventListener('click', async (event) => {
            const button = event.target.closest('[data-favorito-id]');
            if (!button) {
                return;
            }

            if (!IS_AUTHENTICATED) {
                window.location.href = LOGIN_URL;
                return;
            }

            button.disabled = true;
            const originalText = button.textContent;
            button.textContent = 'Salvando...';

            try {
                const favoritoResponse = await fetch(FAVORITOS_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        referencia_tipo: 'galeria',
                        referencia_id: Number(button.dataset.favoritoId),
                    }),
                    credentials: 'same-origin',
                });

                const data = await favoritoResponse.json().catch(() => ({}));

                if (!favoritoResponse.ok) {
                    throw new Error(data.message || 'Nao foi possivel salvar este favorito.');
                }

                button.textContent = 'Favoritado';
                button.className = 'btn-primary px-4 py-2.5';
                showFeedback(data.message || 'Galeria adicionada aos favoritos.');
            } catch (error) {
                button.disabled = false;
                button.textContent = originalText;
                showFeedback(error.message || 'Erro ao adicionar favorito.', 'error');
            }
        });
    } catch (err) {
        console.error(err);
        container.innerHTML = '<div class="app-alert app-alert-error">Erro ao conectar ao servidor.</div>';
    }
});
</script>
@endsection
