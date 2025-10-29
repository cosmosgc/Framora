@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div id="galeria-details"></div>

    <div class="mt-8">
        <h2 class="text-xl font-bold mb-3">Fotos</h2>
        <div id="fotos-list" class="grid grid-cols-1 md:grid-cols-3 gap-4"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const BASE_URL = "{{ url('/') }}";
    const galeriaId = {{ $id }};
    const galeriaDiv = document.getElementById('galeria-details');
    const fotosDiv = document.getElementById('fotos-list');

    galeriaDiv.innerHTML = '<p>Carregando detalhes...</p>';
    fotosDiv.innerHTML = '<p>Carregando fotos...</p>';

    try {
        // Galeria
        const resGaleria = await fetch(`${BASE_URL}/api/galerias/${galeriaId}`);
        const galeriaData = await resGaleria.json();

        if (!galeriaData.success) {
            galeriaDiv.innerHTML = '<p>Galeria não encontrada.</p>';
            return;
        }

        const galeria = galeriaData.data;
        galeriaDiv.innerHTML = `
            <h1 class="text-3xl font-bold mb-2">${galeria.nome}</h1>
            <p class="text-gray-700">${galeria.descricao ?? ''}</p>
            <p class="text-sm text-gray-500 mt-1">${galeria.local ?? ''}</p>
            <img src="/storage/${galeria.banner?.imagem ?? 'placeholder.jpg'}" class="w-full h-64 object-cover rounded mt-3" alt="${galeria.nome}">
        `;

        // Fotos da galeria
        const resFotos = await fetch(`${BASE_URL}/api/fotos?galeria_id=${galeriaId}`);
        const fotosData = await resFotos.json();

        if (!fotosData.success || !fotosData.data.length) {
            fotosDiv.innerHTML = '<p>Nenhuma foto encontrada nesta galeria.</p>';
            return;
        }

        fotosDiv.innerHTML = fotosData.data.map(foto => `
            <a href="${BASE_URL}/galerias/${galeriaId}/fotos/${foto.id}" class="block border rounded shadow-sm hover:shadow-md transition">
                <img src="/storage/${foto.caminho}" alt="${foto.nome}" class="w-full h-48 object-cover rounded-t">
                <div class="p-2">
                    <h3 class="font-semibold text-sm">${foto.nome}</h3>
                </div>
            </a>
        `).join('');

    } catch (err) {
        console.error(err);
        galeriaDiv.innerHTML = '<p>Erro ao carregar dados.</p>';
    }
});
</script>
@endsection
