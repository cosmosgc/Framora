@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow rounded-lg p-6 mt-6">
    <h1 class="text-2xl font-bold mb-4">Editar Galeria — {{ $galeria->nome ?? '—' }}</h1>

    <form id="galeriaForm" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
    <input type="hidden" name="galeria_id" value="{{ $galeria->id }}">

    {{-- Nome --}}
    <div class="mb-4">
        <label class="block font-semibold mb-1">Nome</label>
        <input type="text"
               name="nome"
               class="w-full border rounded px-3 py-2"
               value="{{ old('nome', $galeria->nome) }}"
               required>
    </div>

    {{-- Descrição --}}
    <div class="mb-4">
        <label class="block font-semibold mb-1">Descrição</label>
        <textarea name="descricao"
                  class="w-full border rounded px-3 py-2"
                  rows="3">{{ old('descricao', $galeria->descricao) }}</textarea>
    </div>

    {{-- Categoria --}}
    <div class="mb-4">
        <label class="block font-semibold mb-1">Categoria</label>

        <select name="categoria_id"
                class="w-full border rounded px-3 py-2">
            <option value="">Selecione...</option>
            @foreach($categorias as $cat)
                <option value="{{ $cat->id }}"
                        {{ $galeria->categoria_id == $cat->id ? 'selected' : '' }}>
                    {{ $cat->nome }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Banner (select) --}}
    <div class="mb-4">
        <label class="block font-semibold mb-1">Banner</label>
        <select name="banner_id" class="w-full border rounded px-3 py-2">
            <option value="">Nenhum</option>
            @foreach($banners as $banner)
                <option value="{{ $banner->id }}"
                    {{ $galeria->banner_id == $banner->id ? 'selected' : '' }}>
                    Banner #{{ $banner->id }}
                </option>
            @endforeach
        </select>
        <p class="text-sm text-gray-600 mt-1">Se quiser, posso trocar isso por upload de imagem.</p>
    </div>

    {{-- Local --}}
    <div class="mb-4">
        <label class="block font-semibold mb-1">Local</label>
        <input type="text"
               name="local"
               class="w-full border rounded px-3 py-2"
               value="{{ old('local', $galeria->local) }}">
    </div>

    {{-- Data --}}
    <div class="mb-4">
        <label class="block font-semibold mb-1">Data</label>
        <input type="date"
               name="data"
               class="w-full border rounded px-3 py-2"
               value="{{ old('data', $galeria->data) }}">
    </div>

    {{-- Tempo de duração --}}
    <div class="mb-4">
        <label class="block font-semibold mb-1">Tempo de duração</label>
        <input type="text"
               name="tempo_duracao"
               class="w-full border rounded px-3 py-2"
               value="{{ old('tempo_duracao', $galeria->tempo_duracao) }}"
               placeholder="Ex: 2h, 3 horas, 45min">
    </div>

    {{-- Valor por foto --}}
    <div class="mb-4">
        <label class="block font-semibold mb-1">Valor da Foto (R$)</label>
        <input type="number"
               step="0.01"
               name="valor_foto"
               class="w-full border rounded px-3 py-2"
               value="{{ old('valor_foto', $galeria->valor_foto) }}">
    </div>

    {{-- Upload de fotos --}}
    <div class="mb-4">
        <label class="block font-semibold mb-1">Adicionar Fotos</label>
        <input type="file" name="fotos[]" multiple accept="image/*">
        <p class="text-sm text-gray-600 mt-1">
            As novas fotos serão adicionadas ao final.  
            Você pode ordenar manualmente as fotos abaixo.
        </p>
    </div>

    {{-- Submit --}}
    <div class="flex items-center gap-3">
        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded">
            Salvar alterações
        </button>
        <div id="status"></div>
    </div>
</form>


    <hr class="my-6">

    <h2 class="text-xl font-semibold mb-3">Fotos da galeria</h2>

    <p class="text-sm text-gray-600 mb-3">Arraste para alterar a ordem. A ordem será salva automaticamente ao soltar a foto.</p>

    <div id="fotos-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        @foreach($galeria->fotos()->where('referencia_tipo','galeria')->orderBy('ordem')->get() as $foto)
            @include('galerias._foto_item', ['foto' => $foto])
        @endforeach
    </div>
</div>

<!-- Sortable CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js" integrity="sha512-Tu0h2J... (omitido) ..." crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // URLs (geradas pelo Blade)
    const BASE_UPDATE_URL = "{{ route('galerias.update', $galeria->id) }}";
    const REORDER_URL = "{{ route('galerias.fotos.reorder', $galeria->id) }}";
    const FOTOS_API_URL = "{{ url('/api/fotos') }}"; // ajuste para route('api.fotos.store') se tiver rota nomeada

    // DOM references
    const STATUS_EL = document.getElementById('status');
    const form = document.getElementById('galeriaForm');
    const grid = document.getElementById('fotos-grid');
    const list = document.getElementById('fotos-list');
    const container = grid || list;
    const META_CSRF = document.querySelector('meta[name="csrf-token"]');

    function getCsrfToken() {
        return META_CSRF ? META_CSRF.getAttribute('content') : '';
    }

    function showStatus(html) { if (STATUS_EL) STATUS_EL.innerHTML = html; }
    function clearStatus(delay = 1500) { if (!STATUS_EL) return; setTimeout(()=> { STATUS_EL.innerHTML = ''; }, delay); }

    // Helper: cria o elemento .foto-item no DOM (mesmo HTML do partial)
    function buildFotoItemHtml(foto) {
        // foto: { id, caminho_thumb, caminho_foto, caminho_original }
        const src = foto.caminho_thumb || foto.caminho_foto || foto.caminho_original || '';
        const fotoDiv = document.createElement('div');
        fotoDiv.className = 'foto-item rounded-lg overflow-hidden shadow-sm hover:shadow-md transition relative bg-white';
        fotoDiv.setAttribute('data-id', foto.id);

        fotoDiv.innerHTML = `
            <a href="${foto.caminho_foto ? foto.caminho_foto : src}" target="_blank" class="block">
                <img src="${src}" alt="Foto ${foto.id}" class="w-full h-40 object-cover">
            </a>
            <div class="p-2 flex items-center justify-between gap-2">
                <span class="text-sm text-gray-700">#${foto.id}</span>
                <form class="delete-foto-form" method="POST" action="${foto.destroy_route ?? ('/fotos/' + foto.id)}">
                    <input type="hidden" name="_token" value="${getCsrfToken()}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="button" class="delete-foto-btn text-sm px-2 py-1 bg-red-500 text-white rounded">Excluir</button>
                </form>
            </div>
            <div class="absolute top-2 left-2 text-xs px-2 py-1 bg-black bg-opacity-50 text-white rounded">arrastar</div>
        `;
        return fotoDiv;
    }

    // Inicializa Sortable se estiver disponível (ou reusa existente)
    function initSortable() {
        if (!container) return;
        if (typeof Sortable === 'undefined') {
            console.warn('Sortable não encontrado. Importe o CDN.');
            return;
        }
        // Se já existir uma instância em container._sortable, destrua? (Sortable.create retorna instância)
        // Para simplicidade, criamos se não existir
        if (!container._sortableInstance) {
            container._sortableInstance = Sortable.create(container, {
                animation: 150,
                ghostClass: 'opacity-50',
                draggable: '.foto-item',
                onEnd: async function (evt) {
                    const ids = Array.from(container.querySelectorAll('.foto-item')).map(el => el.getAttribute('data-id'));
                    if (!ids.length) return;
                    showStatus('Salvando ordem...');
                    try {
                        const res = await fetch(REORDER_URL, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken(),
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ ordered_ids: ids })
                        });
                        const json = await res.json();
                        if (json && (json.success === true || json.success === 'true')) {
                            showStatus('<span style="color:green">Ordem salva.</span>');
                        } else {
                            showStatus('<span style="color:red">Erro ao salvar ordem.</span>');
                            console.error('Reorder response', json);
                        }
                    } catch (err) {
                        console.error('Erro ao salvar ordem', err);
                        showStatus('<span style="color:red">Erro de rede ao salvar ordem.</span>');
                    } finally {
                        clearStatus(1200);
                    }
                }
            });
        }
    }

    // Delegated delete handler (funciona para itens existentes e adicionados dinamicamente)
    function bindDeleteDelegate() {
        if (!container) return;
        container.addEventListener('click', async function (e) {
            const btn = e.target.closest('.delete-foto-btn');
            if (!btn) return;
            const formEl = btn.closest('.delete-foto-form');
            if (!formEl) { console.error('Form de delete não encontrado'); return; }
            if (!confirm('Deseja realmente excluir esta foto?')) return;
            try {
                const res = await fetch(formEl.getAttribute('action'), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new FormData(formEl)
                });
                const json = await res.json();
                if (json && (json.success === true || json.success === 'true')) {
                    const item = btn.closest('.foto-item');
                    if (item) {
                        item.style.transition = 'opacity .25s, transform .25s';
                        item.style.opacity = '0';
                        item.style.transform = 'scale(.98)';
                        setTimeout(()=> item.remove(), 260);
                    }
                } else {
                    alert('Erro ao excluir foto.');
                    console.error('Delete response', json);
                }
            } catch (err) {
                console.error('Erro de rede ao excluir foto', err);
                alert('Erro de rede ao excluir foto.');
            }
        });
    }

    // ---------- Form submit (update galeria) + enviar fotos se houver ----------
    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            showStatus('Salvando...');
            try {
                const formData = new FormData(form);
                const res = await fetch(BASE_UPDATE_URL, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: formData
                });

                const data = await res.json();
                if (data && (data.success === true || data.success === 'true')) {
                    showStatus('<span style="color:green">Metadados salvos.</span>');

                    // ---------- Step 2: enviar fotos (se houver) ----------
                    const inputFotos = form.querySelector('input[name="fotos[]"]');
                    const files = inputFotos ? Array.from(inputFotos.files) : [];

                    // Determine galeriaId: prefer value from server (data.galeria_id), fallback to Blade var (JSON-encoded)
                    let galeriaId = (data && data.galeria_id !== undefined && data.galeria_id !== null) ? data.galeria_id : @json($galeria->id);
                    // if galeriaId is null/undefined and Blade var is defined (non-null), use Blade var
                    if ((galeriaId === null || galeriaId === undefined) && @json($galeria->id) !== null) {
                        galeriaId = @json($galeria->id);
                    }

                    if (files.length > 0) {
                        showStatus('Enviando fotos...');
                        const fotosForm = new FormData();
                        for (const file of files) fotosForm.append('fotos[]', file);
                        fotosForm.append('referencia_tipo', 'galeria');
                        // use galeria_id (ou galeria_id) conforme sua API
                        fotosForm.append('galeria_id', galeriaId);

                        // envia para API (ajuste URL se necessário)
                        const fotosRes = await fetch(FOTOS_API_URL, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': getCsrfToken(),
                                // NOT setting Content-Type; browser sets boundary for FormData
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: fotosForm
                        });

                        const fotosData = await fotosRes.json();

                        if (!fotosData || !(fotosData.success === true || fotosData.success === 'true')) {
                            showStatus('<span style="color:orange">Galeria salva, mas erro ao enviar fotos.</span>');
                            console.error('Fotos upload response', fotosData);
                            // opcional: fallback reload
                            // location.reload();
                            return;
                        }

                        // Se a API retornar um array de fotos, inserimos no DOM
                        if (Array.isArray(fotosData.fotos) && fotosData.fotos.length) {
                            for (const foto of fotosData.fotos) {
                                // Se o endpoint não retornar a rota de delete, construímos uma padrão (ajuste se necessário)
                                if (!foto.destroy_route) {
                                    foto.destroy_route = "{{ url('/fotos') }}/" + foto.id;
                                }
                                const node = buildFotoItemHtml(foto);
                                container.appendChild(node);
                            }
                            // re-init sortable (ou garante que a instância reconheça os novos filhos)
                            initSortable();
                            showStatus('<span style="color:green">Fotos enviadas.</span>');
                        } else {
                            // fallback: recarregar para garantir estado
                            showStatus('Fotos enviadas (recarregando)...');
                            // setTimeout(()=> location.reload(), 900);
                        }
                    } else {
                        // sem fotos — apenas sucesso normal
                        showStatus('<span style="color:green">Salvo.</span>');
                    }
                } else {
                    const msg = data?.message ?? 'Falha ao salvar';
                    showStatus(`<span style="color:red">Erro: ${msg}</span>`);
                }
            } catch (err) {
                console.error('Erro ao enviar form', err);
                showStatus('<span style="color:red">Erro ao enviar.</span>');
            } finally {
                clearStatus(2000);
            }
        });
    }

    // Init sortable + delete delegation
    initSortable();
    bindDeleteDelegate();
});
</script>
@endsection
