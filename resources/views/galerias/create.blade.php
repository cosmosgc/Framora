@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl space-y-8 px-4 py-6 sm:px-6 lg:px-8">
    <section class="overflow-hidden rounded-[2rem] border border-stone-200 bg-[linear-gradient(135deg,#1c1917_0%,#312e81_45%,#0f766e_100%)] text-white shadow-[0_35px_90px_-40px_rgba(15,23,42,0.8)]">
        <div class="grid gap-8 px-6 py-8 lg:grid-cols-[minmax(0,1.3fr)_minmax(320px,0.7fr)] lg:px-10 lg:py-10">
            <div class="space-y-5">
                <span class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-cyan-100 backdrop-blur">
                    Nova publicacao
                </span>
                <div class="space-y-3">
                    <h1 class="max-w-3xl text-4xl font-semibold tracking-tight sm:text-5xl">
                        Monte a galeria como se fosse um post.
                    </h1>
                    <p class="max-w-2xl text-base leading-7 text-white/78 sm:text-lg">
                        O banner funciona como capa da publicacao. As fotos da galeria aparecem separadas abaixo, em formato de grade, para evitar confusao entre capa e conteudo.
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-xs uppercase tracking-[0.24em] text-white/60">Etapa 1</p>
                        <p class="mt-2 text-lg font-semibold">Escolha a capa</p>
                        <p class="mt-2 text-sm text-white/75">Banner grande no topo da galeria.</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-xs uppercase tracking-[0.24em] text-white/60">Etapa 2</p>
                        <p class="mt-2 text-lg font-semibold">Preencha o contexto</p>
                        <p class="mt-2 text-sm text-white/75">Nome, descricao, local, data e preco.</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-xs uppercase tracking-[0.24em] text-white/60">Etapa 3</p>
                        <p class="mt-2 text-lg font-semibold">Adicione as fotos</p>
                        <p class="mt-2 text-sm text-white/75">Imagens reais da galeria em mosaico.</p>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-[1.75rem] border border-white/10 bg-black/20 p-5 backdrop-blur">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_35%)]"></div>
                <div class="relative space-y-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-white/60">Preview mental</p>
                    <div class="overflow-hidden rounded-[1.5rem] border border-white/10 bg-white/10 shadow-2xl">
                        <div class="h-40 bg-[linear-gradient(135deg,rgba(253,230,138,0.85),rgba(251,146,60,0.55),rgba(34,211,238,0.4))]"></div>
                        <div class="space-y-3 bg-stone-950/70 p-5">
                            <div class="h-4 w-32 rounded-full bg-white/20"></div>
                            <div class="h-8 w-4/5 rounded-full bg-white/20"></div>
                            <div class="grid grid-cols-3 gap-2 pt-2">
                                <div class="aspect-square rounded-2xl bg-white/15"></div>
                                <div class="aspect-square rounded-2xl bg-white/10"></div>
                                <div class="aspect-square rounded-2xl bg-white/15"></div>
                            </div>
                        </div>
                    </div>
                    <p class="text-sm leading-6 text-white/70">
                        A capa domina o topo. As fotos ficam claramente agrupadas depois, como anexos visuais do post.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <form id="galeriaForm" enctype="multipart/form-data" class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_360px]">
        <input type="hidden" name="user_id" id="user_id_input" value="{{ auth()->id() ?? '' }}">

        <div class="space-y-8">
            <section class="overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-[0_25px_65px_-45px_rgba(28,25,23,0.55)]">
                <div class="border-b border-stone-100 px-6 py-5 sm:px-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-amber-700">Capa da publicacao</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-stone-900">Banner da galeria</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-stone-600">
                        Esta imagem aparece no topo como capa. Ela nao entra na grade de fotos abaixo.
                    </p>
                </div>

                <div class="space-y-5 p-6 sm:p-8">
                    <div id="bannerPreview"
                        class="group relative overflow-hidden rounded-[1.75rem] border border-dashed border-stone-300 bg-stone-100 hidden">
                        <img class="h-72 w-full object-cover sm:h-80" alt="Preview do banner" />
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent p-5 text-white">
                            <p class="text-xs uppercase tracking-[0.28em] text-white/70">Banner da galeria</p>
                            <p class="mt-2 text-2xl font-semibold">Capa selecionada</p>
                            <p class="mt-2 text-sm text-white/80">Use uma imagem horizontal e impactante para abrir a publicacao.</p>
                        </div>
                    </div>

                    <div id="bannerEmptyState" class="rounded-[1.75rem] border border-dashed border-stone-300 bg-[linear-gradient(135deg,#fafaf9,#f5f5f4,#fffbeb)] px-6 py-10 text-center">
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">Sem capa ainda</p>
                        <h3 class="mt-3 text-2xl font-semibold text-stone-900">Escolha uma imagem para o topo da galeria</h3>
                        <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-stone-600">
                            Pense no banner como a vitrine do post. Depois disso, as fotos da galeria entram na grade logo abaixo.
                        </p>
                    </div>

                    <div class="grid gap-4 lg:grid-cols-2">
                        <div class="rounded-[1.5rem] border border-stone-200 bg-stone-50 p-5">
                            <label for="bannerInput" class="block text-sm font-semibold text-stone-900">Enviar nova capa</label>
                            <p class="mt-1 text-sm leading-6 text-stone-600">Envie uma imagem exclusiva para representar a galeria.</p>
                            <input id="bannerInput" type="file" accept="image/*"
                                class="mt-4 block w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-stone-700 file:mr-4 file:rounded-full file:border-0 file:bg-stone-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-stone-700">
                        </div>

                        <div class="rounded-[1.5rem] border border-stone-200 bg-stone-50 p-5">
                            <label for="bannerSelect" class="block text-sm font-semibold text-stone-900">Ou use um banner existente</label>
                            <p class="mt-1 text-sm leading-6 text-stone-600">Selecione um banner ja cadastrado para servir como capa.</p>
                            <select id="bannerSelect" name="banner_id"
                                class="mt-4 w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-stone-700">
                                <option value="">Sem banner</option>
                            </select>
                        </div>
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-[0_25px_65px_-45px_rgba(28,25,23,0.55)]">
                <div class="border-b border-stone-100 px-6 py-5 sm:px-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">Conteudo</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-stone-900">Texto e configuracoes do post</h2>
                </div>

                <div class="grid gap-5 p-6 sm:grid-cols-2 sm:p-8">
                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-stone-900">Nome da galeria</label>
                        <input type="text" name="nome" class="w-full rounded-2xl border border-stone-300 px-4 py-3 text-stone-800 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100" required>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-stone-900">Descricao</label>
                        <textarea name="descricao" rows="5" class="w-full rounded-2xl border border-stone-300 px-4 py-3 text-stone-800 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"></textarea>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-stone-900">Local</label>
                        <input type="text" name="local" class="w-full rounded-2xl border border-stone-300 px-4 py-3 text-stone-800 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-stone-900">Data</label>
                        <input type="date" name="data" class="w-full rounded-2xl border border-stone-300 px-4 py-3 text-stone-800 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-stone-900">Tempo de duracao</label>
                        <input type="text" name="tempo_duracao" class="w-full rounded-2xl border border-stone-300 px-4 py-3 text-stone-800 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-stone-900">Valor por foto</label>
                        <input type="number" name="valor_foto" step="0.01" class="w-full rounded-2xl border border-stone-300 px-4 py-3 text-stone-800 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100" value="0.00">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-stone-900">Categoria</label>
                        <select name="categoria_id" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-stone-800 shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100" required>
                            <option value="">Selecione uma categoria</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-[0_25px_65px_-45px_rgba(28,25,23,0.55)]">
                <div class="border-b border-stone-100 px-6 py-5 sm:px-8">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-emerald-700">Fotos da galeria</p>
                            <h2 class="mt-2 text-2xl font-semibold tracking-tight text-stone-900">Grade de imagens do post</h2>
                        </div>
                        <div id="photoCounter" class="rounded-full bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-800">
                            0 fotos selecionadas
                        </div>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-stone-600">
                        Estas sao as fotos reais da galeria. Elas aparecem como conteudo da publicacao, separadas do banner.
                    </p>
                </div>

                <div class="space-y-5 p-6 sm:p-8">
                    <div class="rounded-[1.5rem] border border-stone-200 bg-stone-50 p-5">
                        <label for="fotosInput" class="block text-sm font-semibold text-stone-900">Adicionar fotos</label>
                        <p class="mt-1 text-sm leading-6 text-stone-600">Selecione varias imagens para montar a grade visual da galeria.</p>
                        <input id="fotosInput" type="file" name="fotos[]" multiple accept="image/*"
                            class="mt-4 block w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-stone-700 file:mr-4 file:rounded-full file:border-0 file:bg-emerald-700 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-emerald-600">
                    </div>

                    <div id="emptyPhotosState" class="rounded-[1.75rem] border border-dashed border-stone-300 bg-[linear-gradient(135deg,#f8fafc,#f0fdf4)] px-6 py-12 text-center">
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Sem fotos ainda</p>
                        <h3 class="mt-3 text-2xl font-semibold text-stone-900">Sua grade vai aparecer aqui</h3>
                        <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-stone-600">
                            Assim que voce enviar imagens, elas serao organizadas abaixo como miniaturas da galeria, deixando claro o que e capa e o que e conteudo.
                        </p>
                    </div>

                    <div id="previewContainer" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3"></div>
                </div>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="sticky top-6 overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-[0_25px_65px_-45px_rgba(28,25,23,0.55)]">
                <div class="border-b border-stone-100 px-6 py-5">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-stone-500">Painel</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-stone-900">Resumo da galeria</h2>
                </div>

                <div class="space-y-5 p-6">
                    <div class="rounded-[1.5rem] bg-stone-900 p-5 text-white">
                        <p class="text-xs uppercase tracking-[0.24em] text-white/60">Estrutura</p>
                        <div class="mt-4 space-y-3 text-sm text-white/85">
                            <div class="flex items-center justify-between gap-4">
                                <span>Banner de capa</span>
                                <span id="bannerStatus" class="font-semibold text-amber-300">Nao definido</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span>Fotos no grid</span>
                                <span id="sidePhotoCounter" class="font-semibold text-cyan-300">0</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span>Categoria</span>
                                <span id="categoryStatus" class="font-semibold text-white/90">A escolher</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[1.5rem] border border-amber-200 bg-amber-50 p-5">
                        <p class="text-sm font-semibold text-amber-800">Dica para evitar confusao</p>
                        <p class="mt-2 text-sm leading-6 text-amber-900/80">
                            Use uma imagem horizontal e heroica no banner. Reserve as fotos do grid para os momentos, angulos e detalhes que compoem a galeria.
                        </p>
                    </div>

                    <button type="submit" id="submitButton" class="inline-flex w-full items-center justify-center rounded-full bg-stone-900 px-6 py-4 text-sm font-semibold text-white transition hover:bg-stone-700 disabled:cursor-not-allowed disabled:opacity-70">
                        Publicar galeria
                    </button>

                    <div id="statusMessage" class="min-h-6 text-sm"></div>
                </div>
            </section>
        </aside>
    </form>
</div>

<script>
const BASE_URL = "{{ url('/') }}";

document.addEventListener('DOMContentLoaded', async () => {
    const categoriaSelect = document.querySelector('[name="categoria_id"]');
    const form = document.getElementById('galeriaForm');
    const status = document.getElementById('statusMessage');
    const submitButton = document.getElementById('submitButton');
    const fotosInput = document.getElementById('fotosInput');
    const previewContainer = document.getElementById('previewContainer');
    const emptyPhotosState = document.getElementById('emptyPhotosState');
    const photoCounter = document.getElementById('photoCounter');
    const sidePhotoCounter = document.getElementById('sidePhotoCounter');

    const bannerSelect = document.getElementById('bannerSelect');
    const bannerInput = document.getElementById('bannerInput');
    const bannerPreview = document.getElementById('bannerPreview');
    const bannerPreviewImg = bannerPreview.querySelector('img');
    const bannerEmptyState = document.getElementById('bannerEmptyState');
    const bannerStatus = document.getElementById('bannerStatus');
    const categoryStatus = document.getElementById('categoryStatus');

    let bannerFile = null;

    function updateBannerState(hasBanner) {
        bannerPreview.classList.toggle('hidden', !hasBanner);
        bannerEmptyState.classList.toggle('hidden', hasBanner);
        bannerStatus.textContent = hasBanner ? 'Definido' : 'Nao definido';
    }

    function updatePhotoCounters() {
        const total = selectedFiles.length;
        photoCounter.textContent = `${total} ${total === 1 ? 'foto selecionada' : 'fotos selecionadas'}`;
        sidePhotoCounter.textContent = String(total);
        emptyPhotosState.classList.toggle('hidden', total > 0);
    }

    bannerSelect.addEventListener('change', () => {
        bannerInput.value = '';
        bannerFile = null;

        const opt = bannerSelect.selectedOptions[0];
        if (!opt || !opt.dataset.img) {
            updateBannerState(false);
            bannerPreviewImg.removeAttribute('src');
            return;
        }

        bannerPreviewImg.src = `${BASE_URL}/storage/${opt.dataset.img}`;
        updateBannerState(true);
    });

    bannerInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) {
            updateBannerState(Boolean(bannerSelect.value));
            return;
        }

        bannerSelect.value = '';
        bannerFile = file;

        const reader = new FileReader();
        reader.onload = (ev) => {
            bannerPreviewImg.src = ev.target.result;
            updateBannerState(true);
        };
        reader.readAsDataURL(file);
    });

    let selectedFiles = [];

    const uid = () => Math.random().toString(36).slice(2, 9);

    try {
        const [categoriaRes, bannerRes] = await Promise.all([
            fetch(`${BASE_URL}/api/categorias`),
            fetch(`${BASE_URL}/api/banners`)
        ]);

        const categoriaData = await categoriaRes.json();
        if (categoriaData.success && Array.isArray(categoriaData.data)) {
            categoriaData.data.forEach((cat) => {
                const option = document.createElement('option');
                option.value = cat.id;
                option.textContent = cat.nome;
                categoriaSelect.appendChild(option);
            });
        }

        const bannerData = await bannerRes.json();
        if (bannerData.success && Array.isArray(bannerData.data)) {
            bannerData.data.forEach((banner) => {
                const option = document.createElement('option');
                option.value = banner.id;
                option.textContent = banner.titulo || `Banner #${banner.id}`;
                option.dataset.img = banner.imagem || '';
                bannerSelect.appendChild(option);
            });
        }
    } catch (e) {
        console.error('Erro ao carregar dados iniciais', e);
    }

    function renderPreviews() {
        previewContainer.innerHTML = '';

        selectedFiles.forEach((item, index) => {
            const wrapper = document.createElement('article');
            wrapper.className = 'overflow-hidden rounded-[1.5rem] border border-stone-200 bg-white shadow-sm';

            const imageFrame = document.createElement('div');
            imageFrame.className = 'relative';

            const img = document.createElement('img');
            img.className = 'h-52 w-full object-cover';
            img.alt = item.file.name;

            const reader = new FileReader();
            reader.onload = (e) => {
                img.src = e.target.result;
            };
            reader.readAsDataURL(item.file);

            const badge = document.createElement('div');
            badge.className = 'absolute left-3 top-3 rounded-full bg-black/70 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-white';
            badge.textContent = `Foto ${index + 1}`;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'absolute right-3 top-3 inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-sm font-bold text-stone-900 shadow transition hover:bg-white';
            removeBtn.innerHTML = '&times;';
            removeBtn.title = 'Remover esta foto';
            removeBtn.addEventListener('click', () => {
                removeFile(item.id);
            });

            const info = document.createElement('div');
            info.className = 'space-y-2 p-4';
            info.innerHTML = `
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-700">Imagem da grade</p>
                <p class="truncate text-sm font-medium text-stone-900">${escapeHtml(item.file.name)}</p>
                <p class="text-xs text-stone-500">${formatBytes(item.file.size)}</p>
            `;

            imageFrame.appendChild(img);
            imageFrame.appendChild(badge);
            imageFrame.appendChild(removeBtn);
            wrapper.appendChild(imageFrame);
            wrapper.appendChild(info);
            previewContainer.appendChild(wrapper);
        });

        updatePhotoCounters();
    }

    function removeFile(id) {
        selectedFiles = selectedFiles.filter((x) => x.id !== id);
        renderPreviews();
    }

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

    function formatBytes(bytes) {
        if (bytes < 1024) return `${bytes} B`;
        if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
        return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
    }

    fotosInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files || []);
        for (const f of files) {
            const already = selectedFiles.some((x) => x.file.name === f.name && x.file.size === f.size && x.file.lastModified === f.lastModified);
            if (!already) {
                selectedFiles.push({ id: uid(), file: f });
            }
        }
        fotosInput.value = '';
        renderPreviews();
    });

    categoriaSelect.addEventListener('change', () => {
        const selectedOption = categoriaSelect.selectedOptions[0];
        categoryStatus.textContent = selectedOption && selectedOption.value ? selectedOption.textContent : 'A escolher';
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        status.innerHTML = '<p class="text-stone-600">Enviando dados...</p>';
        submitButton.disabled = true;
        submitButton.textContent = 'Publicando...';

        try {
            const baseForm = new FormData();
            const formElems = form.querySelectorAll('input[name], textarea[name], select[name]');
            formElems.forEach((el) => {
                if (el.name === 'fotos[]') return;
                if (el.type === 'file') return;
                baseForm.append(el.name, el.value || '');
            });

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
                    status.innerHTML = '<p class="text-red-600">Erro ao criar banner</p>';
                    submitButton.disabled = false;
                    submitButton.textContent = 'Publicar galeria';
                    return;
                }

                bannerId = bannerData.data.id;
            }

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
                submitButton.disabled = false;
                submitButton.textContent = 'Publicar galeria';
                return;
            }

            const galeriaId = galeriaData.data.id;

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
                    status.innerHTML = '<p class="text-yellow-600">Galeria criada, mas houve erro ao enviar as fotos.</p>';
                    submitButton.disabled = false;
                    submitButton.textContent = 'Publicar galeria';
                    return;
                }
            }

            status.innerHTML = '<p class="text-green-600">Galeria criada com sucesso!</p>';
            form.reset();
            selectedFiles = [];
            bannerFile = null;
            bannerPreviewImg.removeAttribute('src');
            updateBannerState(false);
            categoryStatus.textContent = 'A escolher';
            renderPreviews();
        } catch (err) {
            console.error(err);
            status.innerHTML = '<p class="text-red-600">Erro ao enviar dados.</p>';
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Publicar galeria';
        }
    });

    form.addEventListener('reset', () => {
        selectedFiles = [];
        bannerFile = null;
        bannerPreviewImg.removeAttribute('src');
        updateBannerState(false);
        categoryStatus.textContent = 'A escolher';
        renderPreviews();
    });

    updateBannerState(false);
    updatePhotoCounters();
});
</script>
@endsection
