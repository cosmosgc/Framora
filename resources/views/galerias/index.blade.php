@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="text-2xl font-bold mb-4">Galerias</h1>

    <div id="galerias-list" class="grid grid-cols-1 md:grid-cols-3 gap-4"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const container = document.getElementById('galerias-list');
    container.innerHTML = '<p>Carregando galerias...</p>';

    try {
        const response = await fetch('/api/galerias');
        const result = await response.json();

        if (!result.success) {
            container.innerHTML = '<p>Erro ao carregar galerias.</p>';
            return;
        }

        container.innerHTML = result.data.map(galeria => `
            <div class="border rounded-lg shadow p-3 bg-white hover:shadow-md transition">
                <img src="/storage/${galeria.banner?.imagem ?? 'placeholder.jpg'}" alt="${galeria.nome}" class="w-full h-48 object-cover rounded mb-2">
                <h2 class="font-semibold text-lg">${galeria.nome}</h2>
                <p class="text-sm text-gray-600">${galeria.descricao ?? ''}</p>
                <a href="/galerias/${galeria.id}" class="inline-block mt-2 text-blue-600 hover:underline">Ver detalhes</a>
            </div>
        `).join('');
    } catch (err) {
        console.error(err);
        container.innerHTML = '<p>Erro ao conectar ao servidor.</p>';
    }
});
</script>
@endsection
