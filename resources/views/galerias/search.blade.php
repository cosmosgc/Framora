@extends('layouts.app')

@section('content')
<div class="container py-6">
    <h1 class="text-2xl font-bold mb-4">Buscar galerias</h1>

    <form method="GET" action="{{ route('galerias.web.search') }}" class="mb-4">
        <div class="flex gap-2 items-center">
            <input type="search" name="q" id="q" value="{{ $q }}" placeholder="Buscar galerias..." class="flex-1 py-2 px-3 rounded-md border border-gray-200 bg-white text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-md">🔎 Buscar</button>
        </div>
    </form>

    <div id="results-meta" class="mb-3 text-sm text-gray-600"></div>
    <div id="search-results" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"></div>

    <div id="load-more-wrap" class="mt-4 text-center">
        <button id="load-more" class="px-4 py-2 bg-gray-100 rounded-md shadow-sm hidden">Carregar mais</button>
    </div>
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
            resultsContainer.innerHTML = '<p class="text-sm text-gray-500">Digite pelo menos 2 caracteres para buscar.</p>';
            metaEl.textContent = '';
            return;
        }

        try {
            const res = await fetch(`${BASE_URL}/api/galerias?search=${encodeURIComponent(initialQ)}&per_page=${perPage}&page=${p}`);
            const json = await res.json();
            if (!json.success) {
                resultsContainer.innerHTML = '<p class="text-sm text-red-500">Erro ao buscar.</p>';
                return;
            }

            lastPage = json.meta.last_page || 1;
            page = json.meta.current_page || p;

            metaEl.textContent = `Mostrando ${json.data.length} de ${json.meta.total} resultados`;

            const html = json.data.map(g => `
                <div class="border rounded-lg shadow p-3 bg-white hover:shadow-md transition">
                    <a href="${BASE_URL}/galerias/${g.id}">
                        <img src="${BASE_URL}/${g.banner?.imagem ?? 'placeholder.jpg'}" alt="${g.nome}" class="w-full h-40 object-cover rounded mb-2">
                    </a>
                    <h2 class="font-semibold text-lg">${g.nome}</h2>
                    <p class="text-sm text-gray-600">${g.descricao ?? ''}</p>
                    <a href="${BASE_URL}/galerias/${g.id}" class="inline-block mt-2 text-blue-600 hover:underline">Ver detalhes</a>
                </div>
            `).join('');

            if (append) resultsContainer.insertAdjacentHTML('beforeend', html);
            else resultsContainer.innerHTML = html || '<p class="text-sm text-gray-500">Nenhum resultado</p>';

            // load more visibility
            if (page < lastPage) {
                loadMoreBtn.classList.remove('hidden');
            } else {
                loadMoreBtn.classList.add('hidden');
            }

        } catch (e) {
            console.error(e);
            resultsContainer.innerHTML = '<p class="text-sm text-red-500">Erro ao conectar ao servidor.</p>';
        }
    }

    if (initialQ && initialQ.length >=2) {
        await fetchAndRender(1, false);
    } else {
        resultsContainer.innerHTML = '<p class="text-sm text-gray-500">Digite pelo menos 2 caracteres para buscar.</p>';
    }

    loadMoreBtn.addEventListener('click', async () => {
        if (page < lastPage) {
            await fetchAndRender(page + 1, true);
        }
    });
});
</script>
@endsection
