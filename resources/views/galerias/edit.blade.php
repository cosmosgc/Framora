@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow rounded-lg p-6 mt-6">
    <h1 class="text-2xl font-bold mb-4">Editar Galeria — {{ $galeria->nome ?? '—' }}</h1>

    <form id="galeriaForm" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <input type="hidden" name="user_id" id="user_id_input" value="{{ auth()->id() ?? '' }}">
        <div class="mb-4">
            <label class="block font-semibold mb-1">Nome</label>
            <input type="text" name="nome" class="w-full border rounded px-3 py-2" value="{{ old('nome', $galeria->nome) }}" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Descrição</label>
            <textarea name="descricao" class="w-full border rounded px-3 py-2">{{ old('descricao', $galeria->descricao) }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Adicionar Fotos</label>
            <input type="file" name="fotos[]" multiple accept="image/*">
            <p class="text-sm text-gray-600 mt-1">As novas fotos serão adicionadas ao final. Você pode ordenar manualmente as fotos abaixo.</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Salvar alterações</button>
            <div id="status"></div>
        </div>
    </form>

    <hr class="my-6">

    <h2 class="text-xl font-semibold mb-3">Fotos da galeria</h2>

    <p class="text-sm text-gray-600 mb-3">Arraste para alterar a ordem. A ordem será salva automaticamente ao soltar a foto.</p>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        <ul id="fotos-list" class="space-y-3 w-full">
            @foreach($galeria->fotos()->where('referencia_tipo','galeria')->orderBy('ordem')->get() as $foto)
                <li class="foto-item rounded-lg p-2 bg-gray-50 shadow-sm flex flex-col items-center" data-id="{{ $foto->id }}">
                    <img src="{{  asset($foto->caminho_thumb) ?? asset($foto->caminho_foto) ?? asset($foto->caminho_original) }}" alt="Foto {{ $foto->id }}" class="w-full h-40 object-cover rounded mb-2">
                    <div class="w-full flex items-center justify-between gap-2">
                        <span class="text-sm text-gray-700">#{{ $foto->id }}</span>
                        <form class="delete-foto-form" method="POST" action="{{ route('fotos.destroy', $foto->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="delete-foto-btn text-sm px-2 py-1 bg-red-500 text-white rounded">Excluir</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<!-- Sortable CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js" integrity="sha512-Tu0h2J... (omitido) ..." crossorigin="anonymous"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const BASE_UPDATE_URL = "{{ route('galerias.update', $galeria->id) }}"; // atualiza metadados da galeria
    const REORDER_URL = "{{ route('galerias.fotos.reorder', $galeria->id) }}"; // rota para reorder (veja sugestão no controller)
    const STATUS_EL = document.getElementById('status');
    const form = document.getElementById('galeriaForm');

    // Submete alterações da galeria (nome, descricao, novas fotos)
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        STATUS_EL.innerHTML = 'Salvando...';
        try {
            const formData = new FormData(form);
            // fetch usando a rota blade (usa URI correta do projeto)
            const res = await fetch(BASE_UPDATE_URL, {
                method: 'POST', // usamos POST por causa de _method=PUT no form
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });
            const data = await res.json();
            if (data.success ?? false) {
                STATUS_EL.innerHTML = `<p class="text-green-600">Salvo com sucesso.</p>`;
                // opcional: recarregar a página para carregar as novas fotos no list
                // location.reload();
            } else {
                STATUS_EL.innerHTML = `<p class="text-red-600">Erro: ${data.message ?? 'Falha ao salvar'}</p>`;
            }
        } catch (err) {
            console.error(err);
            STATUS_EL.innerHTML = `<p class="text-red-600">Erro ao enviar.</p>`;
        }
    });

    // Inicializa Sortable
    const el = document.getElementById('fotos-list');
    const sortable = Sortable.create(el, {
        animation: 150,
        ghostClass: 'opacity-50',
        onEnd: async function (evt) {
            // monta array com ids na nova ordem
            const ids = Array.from(el.querySelectorAll('.foto-item')).map(li => li.getAttribute('data-id'));
            // envia ao servidor
            try {
                STATUS_EL.innerHTML = 'Salvando ordem...';
                const res = await fetch(REORDER_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ ordered_ids: ids })
                });
                const json = await res.json();
                if (json.success ?? false) {
                    STATUS_EL.innerHTML = `<p class="text-green-600">Ordem salva.</p>`;
                } else {
                    STATUS_EL.innerHTML = `<p class="text-red-600">Erro ao salvar ordem.</p>`;
                }
            } catch (err) {
                console.error(err);
                STATUS_EL.innerHTML = `<p class="text-red-600">Erro de rede.</p>`;
            }
            // limpa status após 2s
            setTimeout(()=> STATUS_EL.innerHTML = '', 2000);
        }
    });

    // Delete foto (botão)
    document.querySelectorAll('.delete-foto-btn').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            const frm = e.target.closest('.delete-foto-form');
            const action = frm.getAttribute('action');
            if (!confirm('Deseja realmente excluir esta foto?')) return;
            try {
                const res = await fetch(action, {
                    method: 'POST', // _method=DELETE in form
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new FormData(frm)
                });
                const json = await res.json();
                if (json.success ?? false) {
                    // remove do DOM
                    e.target.closest('.foto-item').remove();
                } else {
                    alert('Erro ao excluir');
                }
            } catch (err) {
                console.error(err);
                alert('Erro ao excluir (rede)');
            }
        });
    });
});
</script>
@endsection
