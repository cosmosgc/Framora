@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow rounded-lg p-6 mt-6">
    <h1 class="text-2xl font-bold mb-4">Criar Nova Galeria</h1>

    <form id="galeriaForm" enctype="multipart/form-data">
        <input type="hidden" name="user_id" id="user_id_input" value="{{ auth()->id() ?? '' }}">
        <div class="mb-4">
            <label class="block font-semibold mb-1">Nome</label>
            <input type="text" name="nome" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Descrição</label>
            <textarea name="descricao" class="w-full border rounded px-3 py-2"></textarea>
        </div>

        <div class="mb-4 grid grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold mb-1">Local</label>
                <input type="text" name="local" class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block font-semibold mb-1">Data</label>
                <input type="date" name="data" class="w-full border rounded px-3 py-2">
            </div>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Tempo de Duração</label>
            <input type="text" name="tempo_duracao" class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Valor da Foto</label>
            <input type="number" name="valor_foto" step="0.01" class="w-full border rounded px-3 py-2" value="0.00">
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Categoria</label>
            <select name="categoria_id" class="w-full border rounded px-3 py-2" required>
                <option value="">Selecione uma categoria</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Fotos (múltiplas)</label>
            <input type="file" name="fotos[]" multiple accept="image/*" class="w-full border rounded px-3 py-2">
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Criar Galeria
        </button>
    </form>

    <div id="statusMessage" class="mt-4"></div>
</div>

<script>
const BASE_URL = "{{ url('/') }}";

document.addEventListener('DOMContentLoaded', async () => {
    const categoriaSelect = document.querySelector('[name="categoria_id"]');
    const form = document.getElementById('galeriaForm');
    const status = document.getElementById('statusMessage');

    // Load categorias
    try {
        const res = await fetch(`${BASE_URL}/api/categorias`);
        const data = await res.json();
        if (data.success && Array.isArray(data.data)) {
            data.data.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.id;
                option.textContent = cat.nome;
                categoriaSelect.appendChild(option);
            });
        }
    } catch (e) {
        console.error("Erro ao carregar categorias", e);
    }

    // Handle submit
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        status.innerHTML = "Enviando dados...";

        try {
            const formData = new FormData(form);

            // Step 1: Cria a galeria
            const galeriaRes = await fetch(`${BASE_URL}/api/galerias`, {
                method: 'POST',
                body: formData
            });

            const galeriaData = await galeriaRes.json();
            if (!galeriaData.success) {
                status.innerHTML = `<p class="text-red-600">Erro: ${galeriaData.message}</p>`;
                return;
            }

            const galeriaId = galeriaData.data.id;
            const fotos = form.querySelector('[name="fotos[]"]').files;

            // Step 2: Envia as fotos
            if (fotos.length > 0) {
                const fotosForm = new FormData();
                for (const file of fotos) {
                    fotosForm.append('fotos[]', file);
                }
                fotosForm.append('referencia_tipo', 'galeria');
                fotosForm.append('galeria_id', galeriaId);

                const fotosRes = await fetch(`${BASE_URL}/api/fotos`, {
                    method: 'POST',
                    body: fotosForm
                });

                const fotosData = await fotosRes.json();
                if (!fotosData.success) {
                    status.innerHTML = `<p class="text-yellow-600">Galeria criada, mas erro ao enviar fotos.</p>`;
                    return;
                }
            }

            status.innerHTML = `<p class="text-green-600">Galeria criada com sucesso!</p>`;
            form.reset();
        } catch (err) {
            console.error(err);
            status.innerHTML = `<p class="text-red-600">Erro ao enviar dados.</p>`;
        }
    });
});
</script>
@endsection
