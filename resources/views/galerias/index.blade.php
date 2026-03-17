@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="text-2xl font-bold mb-4">Galerias</h1>

    <div id="favorito-feedback" class="hidden mb-4 rounded-lg px-4 py-3 text-sm"></div>
    <div id="galerias-list" class="grid grid-cols-1 md:grid-cols-3 gap-4"></div>
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
    container.innerHTML = '<p>Carregando galerias...</p>';

    const showFeedback = (message, type = 'success') => {
        feedback.className = `mb-4 rounded-lg px-4 py-3 text-sm ${
            type === 'error'
                ? 'bg-red-100 text-red-800'
                : type === 'info'
                    ? 'bg-blue-100 text-blue-800'
                    : 'bg-green-100 text-green-800'
        }`;
        feedback.textContent = message;
        feedback.classList.remove('hidden');
    };

    try {
        const response = await fetch(`${BASE_URL}/api/galerias`);
        const result = await response.json();

        if (!result.success) {
            container.innerHTML = '<p>Erro ao carregar galerias.</p>';
            return;
        }

        container.innerHTML = result.data.map(galeria => `
            <div class="border rounded-lg shadow p-3 bg-white hover:shadow-md transition">
                <img src="${BASE_URL}/${galeria.banner?.imagem ?? 'placeholder.jpg'}" alt="${galeria.nome}" class="w-full h-48 object-cover rounded mb-2">
                <h2 class="font-semibold text-lg">${galeria.nome}</h2>
                <p class="text-sm text-gray-600">${galeria.descricao ?? ''}</p>
                <div class="mt-3 flex items-center justify-between gap-3">
                    <a href="${BASE_URL}/galerias/${galeria.id}" class="inline-block text-blue-600 hover:underline">Ver detalhes</a>
                    <button
                        type="button"
                        class="rounded-lg border border-pink-200 px-3 py-2 text-sm font-medium text-pink-700 hover:bg-pink-50 disabled:cursor-not-allowed disabled:opacity-60"
                        data-favorito-id="${galeria.id}"
                    >
                        Favoritar
                    </button>
                </div>
            </div>
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
                button.classList.remove('border-pink-200', 'text-pink-700', 'hover:bg-pink-50');
                button.classList.add('border-green-200', 'text-green-700', 'bg-green-50');
                showFeedback(data.message || 'Galeria adicionada aos favoritos.');
            } catch (error) {
                button.disabled = false;
                button.textContent = originalText;
                showFeedback(error.message || 'Erro ao adicionar favorito.', 'error');
            }
        });
    } catch (err) {
        console.error(err);
        container.innerHTML = '<p>Erro ao conectar ao servidor.</p>';
    }
});
</script>
@endsection
