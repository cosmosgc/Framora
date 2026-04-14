@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <section class="app-shell px-6 py-8 sm:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl space-y-3">
                <p class="page-eyebrow">Busca</p>
                <h1 class="page-title">Buscar galerias com a mesma estrutura visual das outras páginas.</h1>
                <p class="page-copy">
                    Digite um termo para encontrar coleções, revisar os resultados e carregar mais itens sem sair da página.
                </p>
            </div>

            @if($q)
                <div class="stat-pill">
                    <span>"{{ $q }}"</span>
                    <span class="text-stone-500 dark:text-stone-400">consulta</span>
                </div>
            @endif
        </div>
    </section>

    <section class="app-panel p-6 sm:p-8">
        <form method="GET" action="{{ route('galerias.web.search') }}" class="mb-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <input type="search" name="q" id="q" value="{{ $q }}" placeholder="Buscar galerias..." class="field-input flex-1" />
                <button type="submit" class="btn-primary">Buscar</button>
            </div>
        </form>

        <div id="results-meta" class="mb-4 text-sm text-stone-600 dark:text-stone-300"></div>
        <div id="search-results" class="grid grid-cols-1 gap-5 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"></div>

        <div id="load-more-wrap" class="mt-6 text-center">
            <button id="load-more" class="btn-secondary hidden">Carregar mais</button>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const BASE_URL = "{{ url('/') }}";
    const initialQ = {!! json_encode($q) !!} || '';
    const perPage = 12;
    let page = 1;
    let lastPage = 1;
    const resultsContainer = document.getElementById('search-results');
    const metaEl = document.getElementById('results-meta');
    const loadMoreBtn = document.getElementById('load-more');

    async function fetchAndRender(p = 1, append = false) {
        if (!initialQ || initialQ.length < 2) {
            resultsContainer.innerHTML = '<div class="empty-state"><p class="page-copy">Digite pelo menos 2 caracteres para buscar.</p></div>';
            metaEl.textContent = '';
            return;
        }

        try {
            const res = await fetch(`${BASE_URL}/api/galerias?search=${encodeURIComponent(initialQ)}&per_page=${perPage}&page=${p}`);
            const json = await res.json();
            if (!json.success) {
                resultsContainer.innerHTML = '<div class="app-alert app-alert-error">Erro ao buscar.</div>';
                return;
            }

            lastPage = json.meta.last_page || 1;
            page = json.meta.current_page || p;

            metaEl.textContent = `Mostrando ${json.data.length} de ${json.meta.total} resultados`;

            const html = json.data.map(g => `
                <article class="overflow-hidden rounded-[1.75rem] border border-stone-200 bg-white shadow-[0_18px_38px_-30px_rgba(28,25,23,0.28)] dark:border-stone-800 dark:bg-stone-900">
                    <a href="${BASE_URL}/galerias/${g.id}">
                        <img src="${BASE_URL}/${g.banner?.imagem ?? 'placeholder.jpg'}" alt="${g.nome}" class="h-40 w-full object-cover">
                    </a>
                    <div class="space-y-3 p-5">
                        <h2 class="text-lg font-semibold text-stone-900 dark:text-stone-100">${g.nome}</h2>
                        <p class="text-sm leading-6 text-stone-600 dark:text-stone-300">${g.descricao ?? ''}</p>
                        <a href="${BASE_URL}/galerias/${g.id}" class="btn-secondary px-4 py-2.5">Ver detalhes</a>
                    </div>
                </article>
            `).join('');

            if (append) {
                resultsContainer.insertAdjacentHTML('beforeend', html);
            } else {
                resultsContainer.innerHTML = html || '<div class="empty-state"><p class="page-copy">Nenhum resultado encontrado.</p></div>';
            }

            if (page < lastPage) {
                loadMoreBtn.classList.remove('hidden');
            } else {
                loadMoreBtn.classList.add('hidden');
            }
        } catch (e) {
            console.error(e);
            resultsContainer.innerHTML = '<div class="app-alert app-alert-error">Erro ao conectar ao servidor.</div>';
        }
    }

    if (initialQ && initialQ.length >= 2) {
        await fetchAndRender(1, false);
    } else {
        resultsContainer.innerHTML = '<div class="empty-state"><p class="page-copy">Digite pelo menos 2 caracteres para buscar.</p></div>';
    }

    loadMoreBtn.addEventListener('click', async () => {
        if (page < lastPage) {
            await fetchAndRender(page + 1, true);
        }
    });
});
</script>
@endsection
