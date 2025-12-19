@extends('layouts.admin')

@section('title', 'Fotos da Galeria')
@section('header', 'Galeria: ' . $galeria->nome)

@section('content')
@if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
    </div>
@endif

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet"
      href="https://cdn.datatables.net/2.3.5/css/dataTables.dataTables.min.css">

<div class="mb-4 text-sm text-gray-600">
    <strong>Categoria:</strong> {{ $galeria->categoria->nome ?? '—' }} |
    <strong>Autor:</strong> {{ $galeria->user->name ?? '—' }} |
    <strong>Total de fotos:</strong> {{ $galeria->fotos->count() }}
</div>

<a href="{{ route('admin.galerias.index') }}"
   class="text-sm text-blue-600 hover:underline mb-4 inline-block">
    ← Voltar às galerias
</a>

<table id="fotosTable" class="display w-full">
    <thead>
        <tr>
            <th>ID</th>
            <th>Thumb</th>
            <th>Ativo</th>
            <th>Ordem</th>
            <th>Preview</th>
            <th>Ações</th>
        </tr>
    </thead>

    <tbody>
    @foreach($galeria->fotos as $foto)
        <tr>
            <td>{{ $foto->id }}</td>

            <td>
                <img src="{{ $foto->url_thumb }}"
                    class="h-16 rounded border">
            </td>

            <td>
                @if($foto->ativo)
                    <span class="text-green-600">Sim</span>
                @else
                    <span class="text-red-600">Não</span>
                @endif
            </td>

            <td>{{ $foto->ordem }}</td>

            <td>
                <a href="{{ $foto->url_foto }}"
                target="_blank"
                class="text-blue-600 hover:underline">
                    Abrir
                </a>
            </td>

            <td>
                @if(auth()->user()?->isAdmin())
                    <form action="{{ route('admin.fotos.destroy', $foto->id) }}"
                        method="POST"
                        onsubmit="return confirm('Excluir esta foto? Essa ação não pode ser desfeita.')">
                        @csrf
                        @method('DELETE')

                        <button class="text-red-600 hover:underline text-sm">
                            Excluir
                        </button>
                    </form>
                @else
                    —
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>

</table>

<script src="https://cdn.datatables.net/2.3.5/js/dataTables.min.js"></script>
<script>
new DataTable('#fotosTable', {
    pageLength: 15,
    order: [[3, 'asc']],
    language: {
        search: "Buscar:",
        lengthMenu: "Mostrar _MENU_ fotos",
        info: "Mostrando _START_ a _END_ de _TOTAL_ fotos"
    }
});
</script>

@endsection
