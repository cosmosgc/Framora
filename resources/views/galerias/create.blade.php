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
            <label class="block font-semibold mb-1">Banner da Galeria</label>

            <select id="bannerSelect" name="banner_id"
                class="w-full border rounded px-3 py-2 mb-2" hidden>
                <option value="">Sem banner</option>
            </select>

            <input id="bannerInput" type="file" accept="image/*"
                class="w-full border rounded px-3 py-2">

            <p class="text-sm text-gray-500 mt-1">
                Você pode escolher um banner existente ou enviar um novo.
            </p>
        </div>

        <div id="bannerPreview"
            class="mb-4 hidden border rounded overflow-hidden">
            <img class="w-full h-48 object-cover" />
        </div>


        <div class="mb-4">
            <label class="block font-semibold mb-1">Fotos (múltiplas)</label>
            <input id="fotosInput" type="file" name="fotos[]" multiple accept="image/*" class="w-full border rounded px-3 py-2">
            <p class="text-sm text-gray-500 mt-1">Você pode selecionar várias imagens. Elas aparecerão abaixo para preview.</p>
        </div>

        <!-- Preview container -->
        <div id="previewContainer" class="mb-4 grid grid-cols-3 gap-3"></div>

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
    const fotosInput = document.getElementById('fotosInput');
    const previewContainer = document.getElementById('previewContainer');
    
    const bannerSelect = document.getElementById('bannerSelect');
    const bannerInput = document.getElementById('bannerInput');
    const bannerPreview = document.getElementById('bannerPreview');
    const bannerPreviewImg = bannerPreview.querySelector('img');

    let bannerFile = null;

    bannerSelect.addEventListener('change', () => {
        bannerInput.value = '';
        bannerFile = null;

        const opt = bannerSelect.selectedOptions[0];
        if (!opt || !opt.dataset.img) {
            bannerPreview.classList.add('hidden');
            return;
        }

        bannerPreviewImg.src = `${BASE_URL}/storage/${opt.dataset.img}`;
        bannerPreview.classList.remove('hidden');
    });
    bannerInput.addEventListener('change', e => {
        const file = e.target.files[0];
        if (!file) return;

        bannerSelect.value = '';
        bannerFile = file;

        const reader = new FileReader();
        reader.onload = ev => {
            bannerPreviewImg.src = ev.target.result;
            bannerPreview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    });


    // Array mutável para controlar os arquivos que serão realmente enviados
    let selectedFiles = []; // cada item: { id: uniqueId, file: File }

    // Função utilitária para gerar id único simples
    const uid = () => Math.random().toString(36).slice(2, 9);

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

    // Atualiza o DOM do preview com base em selectedFiles
    function renderPreviews() {
        previewContainer.innerHTML = '';
        if (selectedFiles.length === 0) {
            return;
        }

        selectedFiles.forEach(item => {
            const wrapper = document.createElement('div');
            wrapper.className = 'relative border rounded overflow-hidden';

            const img = document.createElement('img');
            img.className = 'w-full h-32 object-cover';
            img.alt = item.file.name;

            // Ler o arquivo para data URL
            const reader = new FileReader();
            reader.onload = (e) => {
                img.src = e.target.result;
            };
            reader.readAsDataURL(item.file);

            // Botão remover (canto superior direito)
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'absolute top-1 right-1 bg-white bg-opacity-80 rounded-full p-1 shadow hover:bg-opacity-100';
            removeBtn.innerHTML = '&#x2715;'; // X
            removeBtn.title = 'Remover esta foto';
            removeBtn.addEventListener('click', () => {
                removeFile(item.id);
            });

            // Info e nome (abaixo da imagem)
            const info = document.createElement('div');
            info.className = 'p-2 text-xs';
            info.innerHTML = `<div class="truncate">${escapeHtml(item.file.name)}</div>`;

            wrapper.appendChild(img);
            wrapper.appendChild(removeBtn);
            wrapper.appendChild(info);
            previewContainer.appendChild(wrapper);
        });
    }

    // Remove arquivo por id
    function removeFile(id) {
        selectedFiles = selectedFiles.filter(x => x.id !== id);
        renderPreviews();
    }

    // Escapa texto para evitar injeção simples no nome do arquivo exibido
    function escapeHtml(text) {
        return text.replace(/[&<>"'`=\/]/g, function (s) {
            return ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
                '/': '&#x2F;',
                '`': '&#x60;',
                '=': '&#x3D;'
            })[s];
        });
    }

    // Quando usuário seleciona arquivos no input
    fotosInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files || []);
        // Adiciona cada arquivo com um id único
        for (const f of files) {
            // opcional: evitar duplicatas pelo name + size
            const already = selectedFiles.some(x => x.file.name === f.name && x.file.size === f.size && x.file.lastModified === f.lastModified);
            if (!already) {
                selectedFiles.push({ id: uid(), file: f });
            }
        }
        // Limpa input para possibilitar re-seleção do mesmo arquivo mais tarde
        fotosInput.value = '';
        renderPreviews();
    });

    // Submit: usa selectedFiles para enviar fotos
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        status.innerHTML = "Enviando dados...";
        try {
            // Step 1: Cria a galeria (dados exceto fotos)
            const baseForm = new FormData();
            // Adiciona os campos do form, exceto fotos
            const formElems = form.querySelectorAll('input[name], textarea[name], select[name]');
            formElems.forEach(el => {
                if (el.name === 'fotos[]') return;
                // somente inputs que tenham name e não sejam tipo file
                if (el.type === 'file') return;
                if (el.tagName.toLowerCase() === 'select' || el.tagName.toLowerCase() === 'textarea' || el.type !== 'file') {
                    baseForm.append(el.name, el.value || '');
                }
            });
            
            // If user uploaded a new banner → create it first
            let bannerId = bannerSelect.value || null;

            if (bannerFile) {
                const bannerForm = new FormData();
                bannerForm.append('titulo', 'Banner da Galeria');
                bannerForm.append('imagem', bannerFile);

                const bannerRes = await fetch(`${BASE_URL}/api/banners`, {
                    method: 'POST',
                    body: bannerForm
                });

                const bannerData = await bannerRes.json();
                if (!bannerData.success) {
                    status.innerHTML = `<p class="text-red-600">Erro ao criar banner</p>`;
                    return;
                }

                bannerId = bannerData.data.id;
            }


            //Append banner_id to gallery form
            if (bannerId) {
                baseForm.append('banner_id', bannerId);
            }

            const galeriaRes = await fetch(`${BASE_URL}/api/galerias`, {
                method: 'POST',
                body: baseForm
            });

            const galeriaData = await galeriaRes.json();
            if (!galeriaData.success) {
                status.innerHTML = `<p class="text-red-600">Erro: ${galeriaData.message || 'erro ao criar galeria'}</p>`;
                return;
            }

            const galeriaId = galeriaData.data.id;

            // Step 2: Envia as fotos (se houver)
            if (selectedFiles.length > 0) {
                const fotosForm = new FormData();
                for (const item of selectedFiles) {
                    fotosForm.append('fotos[]', item.file, item.file.name);
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
                    // se quiser, você pode oferecer retry aqui
                    return;
                }
            }

            status.innerHTML = `<p class="text-green-600">Galeria criada com sucesso!</p>`;
            // limpar tudo
            form.reset();
            selectedFiles = [];
            renderPreviews();
        } catch (err) {
            console.error(err);
            status.innerHTML = `<p class="text-red-600">Erro ao enviar dados.</p>`;
        }
    });

    // Helper: limpa previews quando o usuário reseta formulário (caso use reset programaticamente)
    form.addEventListener('reset', () => {
        selectedFiles = [];
        renderPreviews();
    });
});
</script>
@endsection
